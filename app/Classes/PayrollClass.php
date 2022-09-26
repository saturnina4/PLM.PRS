<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Classes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Http\Models\JoEmployeeDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\PartTimeEmpDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Models\CPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\EPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Interfaces\PayrollInterface;
use MiSAKACHi\VERACiTY\Traits\GeneratesUuidTrait;

final class PayrollClass implements PayrollInterface {
    use GeneratesUuidTrait;

    protected $computationClass,
              $helperClass,
              $reportDataModel,
              $reportParamsModel;

    public function __construct() {
        $this->computationClass  = new ComputationsClass();
        $this->helperClass       = new HelperClass();
        $this->reportDataModel   = new PTReportDataMdl;
        $this->reportParamsModel = new PTReportParamsMdl;
    }

    public function generalPayroll( int $department, int $reportType, string $year, string $month, array $excluded,
                                    array $overrides ) : Collection {
        try {
            $excludedEmployees = array_map(function ($element) {
                return substr($element, 0, 8);
            }, $excluded);

            $exclusionReasons = [];

            foreach ($excluded as $key => $value) {
                $exclusionReasons[ substr($value, 0, 8) ] = substr($value, 8);
            }

            // Variable Initialization
            $employeeData         = $this->helperClass->getEmployeeData( $reportType, $department, $excludedEmployees );
            $employeeCount        = count( $employeeData['employees'] );
            $gpRegularData        = null;
            $empGsisEmployeeShare = null;
            $empGsisEmployerShare = null;
            $signatories          = null;
            $reportId             = null;
            $usedTranche          = null;
            $trancheVersion       = null;
            $isTaxSysGenerated    = null;

            if( $employeeCount > 0 ) {
                $reportId = $this->helperClass->makeOptimizedUuid( false );

                for( $index = 0; $index < $employeeCount; $index++ ) {
                    // Employee Data Declarations
                    $empNumber       = ( string ) $employeeData['employees'][$index]->employeeNumber;
                    $empName         = ( string ) $employeeData['employees'][$index]->fullName;
                    $empDesignation  = ( string ) $employeeData['employees'][$index]->positionName;
                    $empDependents   = ( int ) ( ( $employeeData['employees'][$index]->empDependents <= 4 ) ? $employeeData['employees'][$index]->empDependents : 4 );
                    $empTaxStatus    = ( int ) $employeeData['employees'][$index]->taxStatusId;
                    $empIsExcluded   = ( int ) $employeeData['employees'][$index]->isExcluded;
                    $empSgStepBypass = ( int ) $employeeData['employees'][$index]->sgStepBypass;
                    $empPositionType = ( int ) $employeeData['employees'][$index]->positionType_id;
                    $empPera         = ( float ) $employeeData['employees'][$index]->peraAllowance;
                    $employeeAge     = ( int ) $employeeData['employees'][$index]->employeeAge;
                    $empExclReason   = 0;

                    // Special Exception for Base Salary
                    if( $empSgStepBypass === 1 ) {
                        $empBaseSalary = ( float ) $employeeData['employees'][$index]->salaryAmount;
                    } else {
                        $empBaseSalary = ( float ) $employeeData['employees'][$index]->salaryValue;
                    }

                    if( $empIsExcluded === 0 ) {
                        // Special Report Parameters - Signatories
                        $signatoriesData = $this->helperClass->getSignatories( "{$year}-{$month}-01" );
                        $signatories     = $signatoriesData['signatories'];

                        // Special Report Parameters - Tranche
                        $trancheData    = $this->helperClass->getActiveTranche();
                        $usedTranche    = $trancheData['tranche'];
                        $trancheVersion = $trancheData['version'];

                        // PhilHealth Related Declarations
                        // $empPhilHealth = ( float ) $this->computationClass->philHealth( $empBaseSalary );
                        $empPhilHealth = $this->computationClass->philHealth( $empBaseSalary );
                        $empPhilHealth_Ps = $empPhilHealth[0];
                        $empPhilHealth_Es = $empPhilHealth[1];

                        // Loans & Other Salary Deduction Related Declarations
                        $deductionData       = $this->helperClass->getEmployeeDeductions( $empNumber );
                        $empPagIbig          = ( float ) ( $deductionData['deductions'][0]->pagIbigPremium   ?? 0.00 );
                        $empLvtPay           = ( float ) ( $deductionData['deductions'][0]->lvtPay           ?? 0.00 );
                        $empLandBank         = ( float ) ( $deductionData['deductions'][0]->landBank         ?? 0.00 );
                        $empPlmPcci          = ( float ) ( $deductionData['deductions'][0]->plmPcci          ?? 0.00 );
                        $empPhilamLife       = ( float ) ( $deductionData['deductions'][0]->philamLife       ?? 0.00 );
                        $empStudyGrant       = ( float ) ( $deductionData['deductions'][0]->studyGrant       ?? 0.00 );
                        $empGsisConsolidated = ( float ) ( $deductionData['deductions'][0]->gsisConsolidated ?? 0.00 );
                        $empGsisEducation    = ( float ) ( $deductionData['deductions'][0]->gsisEducation    ?? 0.00 );
                        $empGsisEmergency    = ( float ) ( $deductionData['deductions'][0]->gsisEmergency    ?? 0.00 );
                        $empGsisPolicy       = ( float ) ( $deductionData['deductions'][0]->gsisPolicy       ?? 0.00 );
                        $empGsisUmidCa       = ( float ) ( $deductionData['deductions'][0]->gsisUmidCa       ?? 0.00 );
                        $empGsisUoliLoan     = ( float ) ( $deductionData['deductions'][0]->gsisUoliLoan     ?? 0.00 );
                        $empGsisUoliPolicy   = ( float ) ( $deductionData['deductions'][0]->gsisUoliPolicy   ?? 0.00 );
                        $empGsisGfal         = ( float ) ( $deductionData['deductions'][0]->gsisGfal         ?? 0.00 );
                        $empGsisMpl          = ( float ) ( $deductionData['deductions'][0]->gsisMpl          ?? 0.00 );
                        $empGsisComputerLoan = ( float ) ( $deductionData['deductions'][0]->gsisComputerLoan ?? 0.00 );
                        $empPagIbigMpl       = ( float ) ( $deductionData['deductions'][0]->pagIbigMpl       ?? 0.00 );
                        $empPagIbigEcl       = ( float ) ( $deductionData['deductions'][0]->pagIbigEcl       ?? 0.00 );
                        $empPagIbigMp2       = ( float ) ( $deductionData['deductions'][0]->pagIbigMp2       ?? 0.00 );
                        $empNhmfc            = ( float ) ( $deductionData['deductions'][0]->nhmfc            ?? 0.00 );
                        $empMaxicare         = ( float ) ( $deductionData['deductions'][0]->maxicare         ?? 0.00 );
                        $empOtherBills       = ( float ) ( $deductionData['deductions'][0]->otherBills       ?? 0.00 );
                        $empManualWhTax      = ( float ) ( $deductionData['deductions'][0]->manualWhTax      ?? 0.00 );
                        $empAgeDeducBypass   = (  int  ) ( $deductionData['deductions'][0]->ageDeducBypass   ?? 0    );

                        // Absences & Tardiness Related Declarations
                        $atDays    = ( int ) ( $deductionData['deductions'][0]->atDays    ?? 0 );
                        $atHours   = ( int ) ( $deductionData['deductions'][0]->atHours   ?? 0 );
                        $atMinutes = ( int ) ( $deductionData['deductions'][0]->atMinutes ?? 0 );

                        /* In this section, we compute for the amount to be deducted from the employees'
                           salary based on the Absences & Tardiness incurred. */
                        $atDeductionData    = $this->computationClass->atDeductions( $empPositionType, $empBaseSalary, $empPera, $atDays, $atHours, $atMinutes, 22, 0.00 );
                        $atSalaryDeductions = $atDeductionData['atDeductions'];
                        $atPeraDeductions   = $atDeductionData['peraDeductions'];

                        // GSIS Life & Retirement Related Declarations
                        $gsisData             = $this->computationClass->gsisLr( $empBaseSalary, $atSalaryDeductions );
                        $empGsisLr            = ( float ) $gsisData['gsis_lr'];
                        $empGsisEmployeeShare = ( float ) $gsisData['gsis_employeeShare'];
                        $empGsisEmployerShare = ( float ) $gsisData['gsis_employerShare'];

                        /* Grand Totals Declaration */

                        /* For employees 65 years old & above, they no longer have GSIS & PAG-IBIG Deductions,
                           or they may choose to still have them, so remove these deductions from them if
                           they want to. */
                        if( ! isset( $employeeAge ) || $employeeAge < 65 || $empAgeDeducBypass == 1 ) {
                            // GSIS Deductions
                            $empGsisTotal           = ( float ) (
                                $empGsisConsolidated +
                                $empGsisEducation    +
                                $empGsisEmergency    +
                                $empGsisLr           +
                                $empGsisPolicy       +
                                $empGsisUmidCa       +
                                $empGsisUoliLoan     +
                                $empGsisUoliPolicy   +
                                $empGsisGfal         +
                                $empGsisMpl          +
                                $empGsisComputerLoan
                            );

                            // PAG-IBIG Deductions
                            $empPagIbigTotal = ( float ) ( $empPagIbig + $empPagIbigMpl + $empPagIbigEcl + $empPagIbigMp2 );
                        } else {
                            // GSIS Deductions
                            $empGsisConsolidated = ( float ) 0.00;
                            $empGsisEducation    = ( float ) 0.00;
                            $empGsisEmergency    = ( float ) 0.00;
                            $empGsisLr           = ( float ) 0.00;
                            $empGsisPolicy       = ( float ) 0.00;
                            $empGsisTotal        = ( float ) 0.00;
                            $empGsisUmidCa       = ( float ) 0.00;
                            $empGsisUoliLoan     = ( float ) 0.00;
                            $empGsisUoliPolicy   = ( float ) 0.00;
                            $empGsisGfal         = ( float ) 0.00;
                            $empGsisMpl          = ( float ) 0.00;
                            $empGsisComputerLoan = ( float ) 0.00;

                            // PAG-IBIG Deductions
                            $empPagIbig      = ( float ) 0.00;
                            $empPagIbigEcl   = ( float ) 0.00;
                            $empPagIbigMpl   = ( float ) 0.00;
                            $empPagIbigMp2   = ( float ) 0.00;
                            $empPagIbigTotal = ( float ) 0.00;
                        }

                        // Other Bills Total
                        $empOtherBillsTotal   = ( float ) ( $empNhmfc + $empOtherBills );

                        // Gross Salary
                        $empGrossSalary  = ( float ) ( ( $empBaseSalary + $empLvtPay + $empPera ) - ( $atSalaryDeductions + $atPeraDeductions ) );

                        // Taxable Salary Computation
                        if( $employeeAge >= 65 ) {
                            $empTaxableSalary = ( float ) (
                                $empBaseSalary      -
                                $atSalaryDeductions -
                                $empGsisLr          -
                                $empPhilHealth_Ps
                            );
                        } else {
                            $empTaxableSalary = ( float ) (
                                $empBaseSalary      -
                                $atSalaryDeductions -
                                $empGsisLr          -
                                $empPhilHealth_Ps   -
                                env( 'TAXRATES_PAGIBIG_PREMIUM', 100 )
                            );
                        }

                        // Tax Related Declarations
                        $whTax = $this->computationClass->withHoldingTax( $empTaxableSalary, $empDependents, $empTaxStatus );

                        // Check if Tax Automatic Computation is overridden
                        if( $overrides['whTax'] === true ) {
                            $empWhTax = ( float ) $empManualWhTax;
                            $isTaxSysGenerated = ( int ) 0; // Tax was derived from the deductions table
                        } else {
                            $empWhTax = ( float ) $whTax;
                            $isTaxSysGenerated = ( int ) 1; // Tax was computed by the system
                        }

                        // Net Salary
                        $empNetSalary = ( float ) (
                            $empGrossSalary     -
                            $empGsisTotal       -
                            $empWhTax           -
                            $empPhilHealth_Ps   -
                            $empPagIbigTotal    -
                            $empLandBank        -
                            $empPlmPcci         -
                            $empPhilamLife      -
                            $empStudyGrant      -
                            $empMaxicare        -
                            $empOtherBillsTotal
                        );
                    } else {
                        $empBaseSalary        = null;
                        $empLvtPay            = null;
                        $empPera              = null;
                        $empPagIbig           = null;
                        $empPagIbigEcl        = null;
                        $empPagIbigMpl        = null;
                        $empPagIbigMp2        = null;
                        $empPagIbigTotal      = null;
                        $atSalaryDeductions   = null;
                        $atPeraDeductions     = null;
                        $empGsisEmployeeShare = null;
                        $empGsisEmployerShare = null;
                        $empGsisLr            = null;
                        $empGsisPolicy        = null;
                        $empGsisConsolidated  = null;
                        $empGsisEmergency     = null;
                        $empGsisUmidCa        = null;
                        $empGsisUoliPolicy    = null;
                        $empGsisUoliLoan      = null;
                        $empGsisEducation     = null;
                        $empGsisGfal          = null;
                        $empGsisMpl           = null;
                        $empGsisComputerLoan  = null;
                        $empGsisTotal         = null;
                        $empNhmfc             = null;
                        $empMaxicare          = null;
                        $empPhilHealth_Ps     = null;
                        $empPhilHealth_Es     = null;
                        $empLandBank          = null;
                        $empPlmPcci           = null;
                        $empPhilamLife        = null;
                        $empStudyGrant        = null;
                        $empOtherBills        = null;
                        $empOtherBillsTotal   = null;
                        $empGrossSalary       = null;
                        $empTaxableSalary     = null;
                        $empWhTax             = null;
                        $empNetSalary         = null;
                        $empExclReason        = $exclusionReasons[ $empNumber ];
                    }

                    $gpRegularData[$index] = [
                        'unique_id'           => $this->helperClass->makeOptimizedUuid( false ),
                        'report_id'           => $reportId,
                        'empNumber'           => $empNumber,
                        'empName'             => $empName,
                        'empDesignation'      => $empDesignation,
                        'empBaseSalary'       => round( $empBaseSalary, 2 ),
                        'empLvtPay'           => round( $empLvtPay, 2 ),
                        'empPera'             => round( $empPera, 2 ),
                        'empNetSalary'        => round( $empNetSalary, 2 ),
                        'empGrossSalary'      => round( $empGrossSalary, 2 ),
                        'ded_landBank'        => round( $empLandBank, 2 ),
                        'ded_plmPcci'         => round( $empPlmPcci, 2 ),
                        'ded_philamLife'      => round( $empPhilamLife, 2 ),
                        'ded_studyGrant'      => round( $empStudyGrant, 2 ),
                        'ded_philHealth'      => round( $empPhilHealth_Ps, 2 ),
                        'ded_philHealth_e'    => round( $empPhilHealth_Es, 2 ),
                        'ded_otherBills'      => round( $empOtherBills, 2 ),
                        'ded_otherBillsTotal' => round( $empOtherBillsTotal, 2 ),
                        'ded_nhmfc'           => round( $empNhmfc, 2 ),
                        'ded_maxicare'        => round( $empMaxicare, 2 ),
                        'at_salaryDeductions' => round( $atSalaryDeductions, 2 ),
                        'at_peraDeductions'   => round( $atPeraDeductions, 2 ),
                        'gsis_lr'             => round( $empGsisLr, 2 ),
                        'gsis_policy'         => round( $empGsisPolicy, 2 ),
                        'gsis_consolidated'   => round( $empGsisConsolidated, 2 ),
                        'gsis_emergency'      => round( $empGsisEmergency, 2 ),
                        'gsis_umidCa'         => round( $empGsisUmidCa, 2 ),
                        'gsis_uoliPolicy'     => round( $empGsisUoliPolicy, 2 ),
                        'gsis_uoliLoan'       => round( $empGsisUoliLoan, 2 ),
                        'gsis_education'      => round( $empGsisEducation, 2 ),
                        'gsis_gfal'           => round( $empGsisGfal, 2 ),
                        'gsis_mpl'            => round( $empGsisMpl, 2 ),
                        'gsis_computerLoan'   => round( $empGsisComputerLoan, 2 ),
                        'gsis_total'          => round( $empGsisTotal, 2 ),
                        'pi_premium'          => round( $empPagIbig, 2 ),
                        'pi_ecl'              => round( $empPagIbigEcl, 2 ),
                        'pi_mpl'              => round( $empPagIbigMpl, 2 ),
                        'pi_mp2'              => round( $empPagIbigMp2, 2 ),
                        'pi_total'            => round( $empPagIbigTotal, 2 ),
                        'tax_taxableSalary'   => round( $empTaxableSalary, 2 ),
                        'tax_exemptionStatus' => $empTaxStatus,
                        'tax_dependents'      => $empDependents,
                        'tax_whTax'           => round( $empWhTax, 2 ),
                        'isExcluded'          => $empIsExcluded,
                        'exclusionReason'     => $empExclReason,
                        'isTaxSysGenerated'   => $isTaxSysGenerated
                    ];
                }
            }

            $returnValue = [
                'params' => [
                    'report_id'          => $reportId,
                    'gsis_employeeShare' => $empGsisEmployeeShare,
                    'gsis_employerShare' => $empGsisEmployerShare,
                    'signatories'        => $signatories,
                    'usedTranche'        => $usedTranche,
                    'trancheVersion'     => $trancheVersion
                ],
                'data'   => $gpRegularData
            ];
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return collect( $returnValue );
    }

    // Part-time Payroll
    public function partTimePayroll( int $department, string $year, string $month, array $included ) {
        try {
            // Variable Initialization
            $partTimeData = null;

            $reportId = $this->makeOptimizedUuid();

            if ( count( $included ) > 0 ) {
                $partTimeEmpDetailsModel = new PartTimeEmpDetailsMdl;
                $yearMonth = "{$year}-{$month}";

                foreach ( $included as $key => $value ) {
                    $data = $this->indivPartTime( $value, $yearMonth, $department, 'add' );

                    if ( ! is_array( $data ) ) {
                        return $data;
                    }

                    $partTimeData[] = [
                        'unique_id'         => $this->makeOptimizedUuid(),
                        'report_id'         => $reportId,
                        'emp_id'            => $value['empId'],
                        'empNumber'         => $data['empNumber'],
                        'empName'           => $data['empName'],
                        'empDesignation'    => $data['empDesignation'],
                        'empAcademicType'   => $data['empAcademicType'],
                        'empHourlyRate'     => $data['empHourlyRate'],
                        'empNoOfHrs'        => $data['empNoOfHrs'],
                        'empEarnedAmount'   => $data['empEarnedAmount'],
                        'empNetAmount'      => $data['empNetAmount'],
                        'tax_percentage'    => $data['tax_percentage'],
                        'tax_ewt'           => $data['tax_ewt'],
                        'tax_whTax'         => $data['tax_whTax'],
                        'otherDeductions'   => $data['otherDeductions'],
                        'isTaxSysGenerated' => $data['isTaxSysGenerated'],
                        'yearMonth'         => $data['yearMonth'],
                        'remarks'           => $data['remarks']
                    ];
                }
            }

            $returnValue = [
                'params' => [
                    'report_id'      => $reportId
                ],
                'data'   => $partTimeData
            ];
        } catch( \PDOException $p ) {\Log::info($p->getMessage());
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
            return response()-json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        } catch( \Throwable $t ) {\Log::info($t->getMessage());
            throw new \Exception( $t->getMessage() );
        }

        return collect( $returnValue );
    }

    // Individual Part-time
    public function indivPartTime( array $input, string $yearMonth, $department, $mode ) {
        try {
            $partTimeEmpDetailsModel = new PartTimeEmpDetailsMdl;

            $employee      = $partTimeEmpDetailsModel->find( $input['empId'] );
            $empNumber     = $employee->employeeNumber;
            $empHourlyRate = $employee->hourlyRate;

            /* from input */
            $empNoOfHrs      = $input['noOfHrs'];
            $remarks         = $input['remarks'];
            $empTaxPercent   = $input['taxPercent'];
            $otherDeductions = ! empty( $input['otherDeduc'] ) ? $input['otherDeduc'] : 0.0;

            /* Earned Amount */
            $empEarnedAmount = round( $empHourlyRate * $empNoOfHrs, 2 );

            $empEwt   = 0;
            $empWhTax = 0;
            $isTaxSysGenerated = 0;

            /* Remarks for year and month of included employees from previous months,
                placed above computations to be used for computing withholding tax of PLM employee */
            if ( $input['yearMonth'] == $yearMonth || $input['yearMonth'] == '' ) {
                $empYearMonth = $yearMonth;
            } else {
                $empYearMonth = $input['yearMonth'];
            }

            $employeeData = $this->reportDataModel
                ->join( "{$this->reportParamsModel->table} as p", 'report_id', '=', 'p.unique_id' )
                ->where( 'emp_id', $input['empId'] )
                ->where( 'yearMonth', $empYearMonth )
                ->where( 'p.department', $department )
                ->get();

            if ( $mode == 'add' ) {
                if ( count( $employeeData ) > 0 && $empYearMonth == $yearMonth ) {
                    return 'Employee already have a record in the given month and department.';
                }
            } elseif ( $mode == 'edit' ) {
                if ( count( $employeeData ) > 0 && $empYearMonth != $yearMonth ) {
                    // return 'Employee already have a record in the given month and department.' . $empYearMonth . $yearMonth;
                }
            }

            if ( $employee->isOfficialInPlm == 1 ) {
                $yearMonthDate = \Carbon\Carbon::createFromDate(
                    substr( $empYearMonth, 0, 4 ), substr( $empYearMonth, 5, 2 ), 1
                );
                $officialStartDate = \Carbon\Carbon::createFromDate(
                    substr( $employee->officialStartDate, 0, 4 ), substr( $employee->officialStartDate, 5, 2 ), substr( $employee->officialStartDate, 8, 2 )
                );
                $officialEndDate = \Carbon\Carbon::createFromDate( null );
            }

            /* Computation of Tax and Net Amount */
            if ( $employee->isOfficialInPlm == 1 &&
                $yearMonthDate->between( $officialStartDate, $officialEndDate ) &&
                $employee->officialFPaymentComplete
            ) {
                /* if permanent in PLM, withholding tax */
                if ( is_numeric($empTaxPercent) && $empTaxPercent >= 0 ) {
                    /* if witholding tax will be overridden */
                    $empWhTax = round( $empEarnedAmount * ( $empTaxPercent / 100 ), 2 );

                    $isTaxSysGenerated = 1;
                } else {
                    /* if witholding tax will not be overridden */
                    $empWhTax = $this->computationClass->partTimeWithHoldingTax(
                        $empEarnedAmount, $empNumber, $empYearMonth
                    );
                    if ( preg_match( '/^[a-z .\-]+$/i', ( string ) $empWhTax ) ) {
                        return $empWhTax;
                    }
                    $empWhTax = round( $empWhTax, 2 );
                }

                /* subtract tax from earned amount */
                $empNetAmount = $empEarnedAmount - $empWhTax;
            } else {
                /* if not permanent in PLM */
                if ( is_numeric($empTaxPercent) && $empTaxPercent >= 0 ) {
                    /* if witholding tax will be overridden, withholding tax */
                    $empEwt = round( $empEarnedAmount * ( $empTaxPercent / 100 ), 2 );

                    $isTaxSysGenerated = 1;
                } else {
                    /* if witholding tax will not be overridden, extended withholding tax */
                    $empEwt = round( $empEarnedAmount * 0.10, 2 );
                }
                /* subtract tax from earned amount */
                $empNetAmount = $empEarnedAmount - $empEwt;
            }

            /* subtract other deductions from the net amount */
            $empNetAmount -= $otherDeductions;

            return [
                'empNumber'         => $employee->employeeNumber,
                'empName'           => $employee->fullName,
                'empDesignation'    => $employee->positionName,
                'empAcademicType'   => $employee->academicType,
                'empHourlyRate'     => round( $empHourlyRate, 2 ),
                'empNoOfHrs'        => $empNoOfHrs,
                'empEarnedAmount'   => ( string ) round( $empEarnedAmount, 2 ),
                'empNetAmount'      => ( string ) round( $empNetAmount, 2 ),
                'tax_percentage'    => $empTaxPercent,
                'tax_ewt'           => ( string ) round( $empEwt, 2 ),
                'tax_whTax'         => ( string ) round( $empWhTax, 2 ),
                'otherDeductions'   => ( string ) round( $otherDeductions, 2 ),
                'isTaxSysGenerated' => $isTaxSysGenerated,
                'yearMonth'         => $empYearMonth,
                'remarks'           => $remarks
            ];
        } catch( \PDOException $p ) {\Log::info($p->getMessage());
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
            return response()-json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        } catch( \Throwable $t ) {\Log::info($t->getMessage());
            throw new \Exception( $t->getMessage() );
        }
    }

    // Casual Payroll
    public function casualPayroll( int $cutOffPeriod, string $year, string $month, array $included ) {
        try {
            // Variable Initialization
            $casualData = null;

            $reportId = $this->makeOptimizedUuid();

            if ( count( $included ) > 0 ) {
                $yearMonth = "{$year}-{$month}";
                // dd($included);
                foreach ( $included as $key => $value ) {
                    $data = $this->individualCasual( $value, $yearMonth, $cutOffPeriod, 'add' );

                    if ( ! is_array( $data ) ) {
                        return $data;
                    }
     
                    $casualData[] = [
                        'unique_id'           => $this->makeOptimizedUuid(),
                        'report_id'           => $reportId,
                        'empNumber'           => $data['empNumber'],
                        'empName'             => $data['empName'],
                        'empDesignation'      => $data['empDesignation'],
                        'empDepartment'       => $data['empDepartment'],
                        'empDailySalary'      => $data['empDailySalary'],
                        'empNoOfDays'         => $data['empNoOfDays'],
                        'empPartialPayment'   => $data['empPartialPayment'],
                        'empPera'             => $data['empPera'],
                        'empGrossSalary'      => $data['empGrossSalary'],
                        'empNetSalary'        => $data['empNetSalary'],
                        'at_hours'            => $data['atHours'],
                        'at_minutes'          => $data['atMinutes'],
                        'ded_plmPcci'         => round( $data['empPlmPcci'] ?? 0.0, 2 ),
                        'ded_philHealth'      => round( $data['empPhilHealth'] ?? 0.0, 2 ),
                        'ded_otherBills'      => round( $data['empOtherBills'] ?? 0.0, 2 ),
                        'ded_maxicare'        => round( $data['$empMaxicare'] ?? 0.0, 2 ),
                        'at_salaryDeductions' => round( $data['atSalaryDeductions'] ?? 0.0, 2 ),
                        'at_peraDeductions'   => round( $data['atPeraDeductions'] ?? 0.0, 2 ),
                        'gsis_lr'             => round( $data['empGsisLr'] ?? 0.0, 2 ),
                        'gsis_policy'         => round( $data['empGsisPolicy'] ?? 0.0, 2 ),
                        'gsis_consolidated'   => round( $data['empGsisConsolidated'] ?? 0.0, 2 ),
                        'gsis_emergency'      => round( $data['empGsisEmergency'] ?? 0.0, 2 ),
                        'gsis_education'      => round( $data['empGsisEducation'] ?? 0.0, 2 ),
                        'pi_premium'          => round( $data['empPagIbig'] ?? 0.0, 2 ),
                        'pi_ecl'              => round( $data['empPagIbigEcl'] ?? 0.0, 2 ),
                        'pi_mpl'              => round( $data['empPagIbigMpl'] ?? 0.0, 2 ),
                        'tax_taxableSalary'   => round( $data['empTaxableSalary'] ?? 0.0, 2 ),
                        'tax_whTax'           => round( $data['empWhTax'] ?? 0.0, 2 ),
                    ];
                }
            }

            $returnValue = [
                'params' => [
                    'report_id' => $reportId
                ],
                'data' => $casualData
            ];
        } catch( \PDOException $p ) {
            \Log::info($p->getMessage());
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
            return response()-json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        } catch( \Throwable $t ) {
            \Log::info($t);
            throw new \Exception( $t->getMessage() );
        }

        return collect( $returnValue );
    }

    // Individual Casual
    public function individualCasual( array $input, string $yearMonth, $cutOffPeriod, $mode ) {
        try {
            $employeeDetailsModel = new EmployeeDetailsMdl;

            $employee = $employeeDetailsModel
                        ->where( 'employeeNumber', $input['empId'] )
                        ->get();
            if ( count( $employee ) > 1 ) {
                throw new \Exception( 'Employee number: ' . $input['empId'] . ' has multiple records.' );
            }                

            $employee     = $employee[0];
            $empNumber    = $employee->employeeNumber;
            $empDailyRate = (float) bcdiv( $employee->salaryValue, '22', 2 );
            
            /* from input */
            $empNoOfDays = $input['noOfDays'];

            /* Partial Payment */
            $empPartialPayment = round( $empDailyRate * $empNoOfDays, 2 );
            
            $empPera = 0;
            
            // Loans & Other Salary Deduction Related Declarations
            $deductionData       = $this->helperClass->getEmployeeDeductions( $empNumber );
            $empPagIbig          = ( float ) env( 'TAXRATES_PAGIBIG_PREMIUM', 100 );
            $empPlmPcci          = ( float ) ( $deductionData['deductions'][0]->plmPcci          ?? 0.00 );
            $empGsisConsolidated = ( float ) ( $deductionData['deductions'][0]->gsisConsolidated ?? 0.00 );
            $empGsisEducation    = ( float ) ( $deductionData['deductions'][0]->gsisEducation    ?? 0.00 );
            $empGsisEmergency    = ( float ) ( $deductionData['deductions'][0]->gsisEmergency    ?? 0.00 );
            $empGsisPolicy       = ( float ) ( $deductionData['deductions'][0]->gsisPolicy       ?? 0.00 );
            $empPagIbigMpl       = ( float ) ( $deductionData['deductions'][0]->pagIbigMpl       ?? 0.00 );
            $empPagIbigEcl       = ( float ) ( $deductionData['deductions'][0]->pagIbigEcl       ?? 0.00 );
            $empNhmfc            = ( float ) ( $deductionData['deductions'][0]->nhmfc            ?? 0.00 );
            $empMaxicare         = ( float ) ( $deductionData['deductions'][0]->maxicare         ?? 0.00 );
            $empOtherBills       = ( float ) ( $deductionData['deductions'][0]->otherBills       ?? 0.00 );

            // Absences & Tardiness Related Declarations
            $atHours   = ( int ) ( $deductionData['deductions'][0]->atHours   ?? 0 );
            $atMinutes = ( int ) ( $deductionData['deductions'][0]->atMinutes ?? 0 );
            
            $empGsisLr     = ( float ) ( $this->computationClass->gsisLr( ( float ) $employee->salaryValue, 0 ) )['gsis_lr'];
            $empPhilHealth = ( float ) ( $this->computationClass->philHealth( ( float ) $employee->salaryValue ) )[0];

            $deductions = [];

            if ( $cutOffPeriod == 1 ) {
                $empGrossSalary = $empPartialPayment;
                $empNetSalary   = $empGrossSalary;

                $atDeductionData    = $this->computationClass->atDeductions( 1, ( float ) $employee->salaryValue, $empPera, 0, $atHours, $atMinutes, 22, 0.00 );
                $atSalaryDeductions = $atDeductionData['atDeductions'];
                
                if ( $empNetSalary - $empGsisLr >= 2500 ) {
                    $empNetSalary -= $empGsisLr;
                    $deductions['empGsisLr'] = $empGsisLr;
                }
                if ( $empNetSalary - $empPhilHealth >= 2500 ) {
                    $empNetSalary -= $empPhilHealth;
                    $deductions['empPhilHealth'] = $empPhilHealth;
                }
                if ( $empNetSalary - $atSalaryDeductions >= 2500 ) {
                    $empNetSalary -= $atSalaryDeductions;
                    $deductions['atSalaryDeductions'] = $atSalaryDeductions;
                }
                if ( $empNetSalary - $empPagIbig >= 2500 ) {
                    $empNetSalary -= $empPagIbig;
                    $deductions['empPagIbig'] = $empPagIbig;
                }
                if ( $empPlmPcci > 0 && $empNetSalary - $empPlmPcci >= 2500 ) {
                    $empNetSalary -= $empPlmPcci;
                    $deductions['empPlmPcci'] = $empPlmPcci;
                }
                if ( $empGsisConsolidated > 0 && $empNetSalary - $empGsisConsolidated >= 2500 ) {
                    $empNetSalary -= $empGsisConsolidated;
                    $deductions['empGsisConsolidated'] = $empGsisConsolidated;
                }
                if ( $empGsisEducation > 0 && $empNetSalary - $empGsisEducation >= 2500 ) {
                    $empNetSalary -= $empGsisEducation;
                    $deductions['empGsisEducation'] = $empGsisEducation;
                }
                if ( $empGsisEmergency > 0 && $empNetSalary - $empGsisEmergency >= 2500 ) {
                    $empNetSalary -= $empGsisEmergency;
                    $deductions['empGsisEmergency'] = $empGsisEmergency;
                }
                if ( $empGsisPolicy > 0 && $empNetSalary - $empGsisPolicy >= 2500 ) {
                    $empNetSalary -= $empGsisPolicy;
                    $deductions['empGsisPolicy'] = $empGsisPolicy;
                }
                if ( $empPagIbigMpl > 0 && $empNetSalary - $empPagIbigMpl >= 2500 ) {
                    $empNetSalary -= $empPagIbigMpl;
                    $deductions['empPagIbigMpl'] = $empPagIbigMpl;
                }
                if ( $empPagIbigEcl > 0 && $empNetSalary - $empPagIbigEcl >= 2500 ) {
                    $empNetSalary -= $empPagIbigEcl;
                    $deductions['empPagIbigEcl'] = $empPagIbigEcl;
                }
                if ( $empMaxicare > 0 && $empNetSalary - $empMaxicare >= 2500 ) {
                    $empNetSalary -= $empMaxicare;
                    $deductions['empMaxicare'] = $empMaxicare;
                }
                if ( $empOtherBills > 0 && $empNetSalary - $empOtherBills >= 2500 ) {
                    $empNetSalary -= $empOtherBills;
                    $deductions['empOtherBills'] = $empOtherBills;
                }
            } elseif ( $cutOffPeriod == 2 ) {
                $totalNoOfDays = $empNoOfDays;
                
                $cpReportSummaryModel = new CPReportSummaryMdl();
                
                $firstQuincenaData = $cpReportSummaryModel
                    ->where( 'earningYear', substr( $yearMonth, 0, 4 ) )
                    ->where( 'earningMonth', substr( $yearMonth, 5, 2 ) )
                    ->where( 'cutOffPeriod', 1 )
                    ->where( 'empNumber', $empNumber )
                    ->get();

                if ( count( $firstQuincenaData ) > 1 ) {
                    dd('error');
                }

                $totalNoOfDays += isset( $firstQuincenaData[0] ) ? $firstQuincenaData[0]->empNoOfDays : 0;

                $atDeductionData    = $this->computationClass->atDeductions( 1, ( float ) $employee->salaryValue, $empPera, 0, $atHours, $atMinutes, 22, 0.00 );
                $atSalaryDeductions = $atDeductionData['atDeductions'];
                
                $totalAtHours = $atHours + isset( $firstQuincenaData[0] ) ? $firstQuincenaData[0]->at_hours : 0;
                $totalAtMinutes = $atMinutes + isset( $firstQuincenaData[0] ) ? $firstQuincenaData[0]->at_minutes : 0;

                $empPera = ( 2000 / 22 * $totalNoOfDays );

                $atDeductionData  = $this->computationClass->atDeductions( 1, ( float ) $employee->salaryValue, $empPera, 0, $totalAtHours, $totalAtMinutes, 22, 0.00 );
                $atPeraDeductions = $atDeductionData['peraDeductions'];
                $deductions['atPeraDeductions'] = $atPeraDeductions;

                $empPera -= $atPeraDeductions;

                $empGrossSalary = $empPartialPayment + $empPera;
                $empNetSalary   = $empGrossSalary;

                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_lr == 0 ) && $empNetSalary - $empGsisLr >= 2500 ) {
                    $empNetSalary -= $empGsisLr;
                    $deductions['empGsisLr'] = $empGsisLr;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_philHealth == 0 ) && $empNetSalary - $empPhilHealth >= 2500 ) {
                    $empNetSalary -= $empPhilHealth;
                    $deductions['empPhilHealth'] = $empPhilHealth;
                }
                if ( $empNetSalary - $atSalaryDeductions >= 2500 ) {
                    $empNetSalary -= $atSalaryDeductions;
                    $deductions['atSalaryDeductions'] = $atSalaryDeductions;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->pi_premium == 0 ) && $empNetSalary - $empPagIbig >= 2500 ) {
                    $empNetSalary -= $empPagIbig;
                    $deductions['empPagIbig'] = $empPagIbig;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_plmPcci == 0 ) && $empPlmPcci > 0 && $empNetSalary - $empPlmPcci >= 2500 ) {
                    $empNetSalary -= $empPlmPcci;
                    $deductions['empPlmPcci'] = $empPlmPcci;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_consolidated == 0 ) && $empGsisConsolidated > 0 && $empNetSalary - $empGsisConsolidated >= 2500 ) {
                    $empNetSalary -= $empGsisConsolidated;
                    $deductions['empGsisConsolidated'] = $empGsisConsolidated;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_education == 0 ) && $empGsisEducation > 0 && $empNetSalary - $empGsisEducation >= 2500 ) {
                    $empNetSalary -= $empGsisEducation;
                    $deductions['empGsisEducation'] = $empGsisEducation;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_emergency == 0 ) && $empGsisEmergency > 0 && $empNetSalary - $empGsisEmergency >= 2500 ) {
                    $empNetSalary -= $empGsisEmergency;
                    $deductions['empGsisEmergency'] = $empGsisEmergency;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_policy == 0 ) && $empGsisPolicy > 0 && $empNetSalary - $empGsisPolicy >= 2500 ) {
                    $empNetSalary -= $empGsisPolicy;
                    $deductions['empGsisPolicy'] = $empGsisPolicy;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->pi_mpl == 0 ) && $empPagIbigMpl > 0 && $empNetSalary - $empPagIbigMpl >= 2500 ) {
                    $empNetSalary -= $empPagIbigMpl;
                    $deductions['empPagIbigMpl'] = $empPagIbigMpl;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->pi_ecl == 0 ) && $empPagIbigEcl > 0 && $empNetSalary - $empPagIbigEcl >= 2500 ) {
                    $empNetSalary -= $empPagIbigEcl;
                    $deductions['empPagIbigEcl'] = $empPagIbigEcl;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_maxicare == 0 ) && $empMaxicare > 0 && $empNetSalary - $empMaxicare >= 2500 ) {
                    $empNetSalary -= $empMaxicare;
                    $deductions['empMaxicare'] = $empMaxicare;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_otherBills == 0 ) && $empOtherBills > 0 && $empNetSalary - $empOtherBills >= 2500 ) {
                    $empNetSalary -= $empOtherBills;
                    $deductions['empOtherBills'] = $empOtherBills;
                }
       
                $empTaxableSalary = ( float ) (
                    $employee->salaryValue -
                    $atSalaryDeductions    -
                    $empGsisLr             -
                    $empPhilHealth         -
                    $empPagIbig
                );
                $empWhTax = $this->computationClass->withHoldingTax( $empTaxableSalary, 0, 0 );
                
                if ( $empNetSalary - $empWhTax >= 2500 ) {
                    $empNetSalary -= $empWhTax;
                    $deductions['empTaxableSalary'] = $empTaxableSalary;
                    $deductions['empWhTax'] = $empWhTax;
                }
            }

            return array_merge( [
                'empNumber'         => $employee->employeeNumber,
                'empName'           => $employee->fullName,
                'empDesignation'    => $employee->positionName,
                'empDepartment'     => $employee->departmentId,
                'empDailySalary'    => round( $empDailyRate, 2 ),
                'empNoOfDays'       => $empNoOfDays,
                'empPartialPayment' => ( string ) round( $empPartialPayment, 2 ),
                'empPera'           => ( string ) round( $empPera, 2 ),
                'empGrossSalary'    => ( string ) round( $empGrossSalary, 2 ),
                'empNetSalary'      => ( string ) round( $empNetSalary, 2 ),
                'atHours'           => $atHours,
                'atMinutes'         => $atMinutes,
            ], $deductions );
        } catch( \PDOException $p ) {
            \Log::info($p);
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
            return response()-json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        } catch( \Throwable $t ) {
            \Log::info($t);
            throw new \Exception( $t->getMessage() );
        }
    }

    // Excluded Payroll
    public function excludedPayroll( int $cutOffPeriod, string $year, string $month, array $included ) {
        try {
            // Variable Initialization
            $casualData = null;

            $reportId = $this->makeOptimizedUuid();

            if ( count( $included ) > 0 ) {
                $yearMonth = "{$year}-{$month}";
                // dd($included);
                foreach ( $included as $key => $value ) {
                    $data = $this->individualExcluded( $value, $yearMonth, $cutOffPeriod, 'add' );

                    if ( ! is_array( $data ) ) {
                        return $data;
                    }

                    $excludedData[] = [
                        'unique_id'           => $this->makeOptimizedUuid(),
                        'report_id'           => $reportId,
                        'empNumber'           => $data['empNumber'],
                        'empName'             => $data['empName'],
                        'empDesignation'      => $data['empDesignation'],
                        'empDepartment'       => $data['empDepartment'],
                        'empBaseSalary'       => $data['empBaseSalary'],
                        'empNoOfDays'         => $data['empNoOfDays'],
                        'empPartialPayment'   => $data['empPartialPayment'],
                        'empGrossSalary'      => $data['empGrossSalary'],
                        'empNetSalary'        => $data['empNetSalary'],
                        'at_days'             => $data['atDays'],
                        'at_hours'            => $data['atHours'],
                        'at_minutes'          => $data['atMinutes'],
                        'ded_plmPcci'         => round( $data['empPlmPcci'] ?? 0.0, 2 ),
                        'empLvtPay'           => round( $data['empLvtPay'] ?? 0.0, 2 ),
                        'empPera'             => round( $data['empPera'] ?? 0.0, 2 ),
                        'ded_landBank'        => round( $data['empLandBank'] ?? 0.0, 2 ),
                        'ded_plmPcci'         => round( $data['empPlmPcci'] ?? 0.0, 2 ),
                        'ded_philamLife'      => round( $data['empPhilamLife'] ?? 0.0, 2 ),
                        'ded_studyGrant'      => round( $data['empStudyGrant'] ?? 0.0, 2 ),
                        'ded_philHealth'      => round( $data['empPhilHealth_Ps'] ?? 0.0, 2 ),
                        'ded_philHealth_e'    => round( $data['empPhilHealth_Es'] ?? 0.0, 2 ),
                        'ded_otherBills'      => round( $data['empOtherBills'] ?? 0.0, 2 ),
                        'ded_nhmfc'           => round( $data['empNhmfc'] ?? 0.0, 2 ),
                        'ded_maxicare'        => round( $data['empMaxicare'] ?? 0.0, 2 ),
                        'at_salaryDeductions' => round( $data['atSalaryDeductions'] ?? 0.0, 2 ),
                        'at_peraDeductions'   => round( $data['atPeraDeductions'] ?? 0.0, 2 ),
                        'gsis_lr'             => round( $data['empGsisLr'] ?? 0.0, 2 ),
                        'gsis_policy'         => round( $data['empGsisPolicy'] ?? 0.0, 2 ),
                        'gsis_consolidated'   => round( $data['empGsisConsolidated'] ?? 0.0, 2 ),
                        'gsis_emergency'      => round( $data['empGsisEmergency'] ?? 0.0, 2 ),
                        'gsis_umidCa'         => round( $data['empGsisUmidCa'] ?? 0.0, 2 ),
                        'gsis_uoliPolicy'     => round( $data['empGsisUoliPolicy'] ?? 0.0, 2 ),
                        'gsis_uoliLoan'       => round( $data['empGsisUoliLoan'] ?? 0.0, 2 ),
                        'gsis_education'      => round( $data['empGsisEducation'] ?? 0.0, 2 ),
                        'gsis_gfal'           => round( $data['empGsisGfal'] ?? 0.0, 2 ),
                        'pi_premium'          => round( $data['empPagIbig'] ?? 0.0, 2 ),
                        'pi_ecl'              => round( $data['empPagIbigEcl'] ?? 0.0, 2 ),
                        'pi_mpl'              => round( $data['empPagIbigMpl'] ?? 0.0, 2 ),
                        'pi_mp2'              => round( $data['empPagIbigMp2'] ?? 0.0, 2 ),
                        'tax_taxableSalary'   => round( $data['empTaxableSalary'] ?? 0.0, 2 ),
                        'tax_whTax'           => round( $data['empWhTax'] ?? 0.0, 2 ),
                    ];
                }
            }

            $returnValue = [
                'params' => [
                    'report_id' => $reportId
                ],
                'data' => $excludedData
            ];
        } catch( \PDOException $p ) {
            \Log::info($p->getMessage());
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
            return response()-json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        } catch( \Throwable $t ) {
            \Log::info($t);
            throw new \Exception( $t->getMessage() );
        }

        return collect( $returnValue );
    }

    // Individual Excluded
    public function individualExcluded( array $input, string $yearMonth, $cutOffPeriod, $mode ) {
        try {
            $employeeDetailsModel = new EmployeeDetailsMdl;

            $employee = $employeeDetailsModel
                        ->where( 'employeeNumber', $input['empId'] )
                        ->get();
            if ( count( $employee ) > 1 ) {
                throw new \Exception( 'Employee number: ' . $input['empId'] . ' has multiple records.' );
            }                

            $employee    = $employee[0];
            $empNumber   = $employee->employeeNumber;
            $empPera     = ( float ) $employee->peraAllowance;
            $employeeAge = ( int ) $employee->employeeAge;

            // Special Exception for Base Salary
            if( ( int ) $employee->sgStepBypass === 1 ) {
                $empBaseSalary = ( float ) $employee->salaryAmount;
            } else {
                $empBaseSalary = ( float ) $employee->salaryValue;
            }
            // $empDailyRate = (float) bcdiv( $employee->salaryValue, '22', 2 );
            $payPeriod = \Carbon\Carbon::createFromDate( substr( $yearMonth, 0, 4 ), substr( $yearMonth, 5, 2 ), 1 );
            $totalDaysInMonth = $payPeriod->daysInMonth;
            if ( $cutOffPeriod == 1 ) {
                $empNoOfDays = 15;
            } else {
                $empNoOfDays = $totalDaysInMonth - 15;   
            }

            /* Partial Payment */
            $empPartialPayment = round( $empBaseSalary / $totalDaysInMonth * $empNoOfDays, 2 );

            // PhilHealth Related Declarations
            $empPhilHealth = $this->computationClass->philHealth( $empBaseSalary );
            $empPhilHealth_Ps = $empPhilHealth[0];
            $empPhilHealth_Es = $empPhilHealth[1];

            // Loans & Other Salary Deduction Related Declarations
            $deductionData       = $this->helperClass->getEmployeeDeductions( $empNumber );
            $empPagIbig          = ( float ) ( $deductionData['deductions'][0]->pagIbigPremium   ?? 0.00 );
            $empLvtPay           = ( float ) ( $deductionData['deductions'][0]->lvtPay           ?? 0.00 );
            $empLandBank         = ( float ) ( $deductionData['deductions'][0]->landBank         ?? 0.00 );
            $empPlmPcci          = ( float ) ( $deductionData['deductions'][0]->plmPcci          ?? 0.00 );
            $empPhilamLife       = ( float ) ( $deductionData['deductions'][0]->philamLife       ?? 0.00 );
            $empStudyGrant       = ( float ) ( $deductionData['deductions'][0]->studyGrant       ?? 0.00 );
            $empGsisConsolidated = ( float ) ( $deductionData['deductions'][0]->gsisConsolidated ?? 0.00 );
            $empGsisEducation    = ( float ) ( $deductionData['deductions'][0]->gsisEducation    ?? 0.00 );
            $empGsisEmergency    = ( float ) ( $deductionData['deductions'][0]->gsisEmergency    ?? 0.00 );
            $empGsisPolicy       = ( float ) ( $deductionData['deductions'][0]->gsisPolicy       ?? 0.00 );
            $empGsisUmidCa       = ( float ) ( $deductionData['deductions'][0]->gsisUmidCa       ?? 0.00 );
            $empGsisUoliLoan     = ( float ) ( $deductionData['deductions'][0]->gsisUoliLoan     ?? 0.00 );
            $empGsisUoliPolicy   = ( float ) ( $deductionData['deductions'][0]->gsisUoliPolicy   ?? 0.00 );
            $empGsisGfal         = ( float ) ( $deductionData['deductions'][0]->gsisGfal         ?? 0.00 );
            $empPagIbigMpl       = ( float ) ( $deductionData['deductions'][0]->pagIbigMpl       ?? 0.00 );
            $empPagIbigEcl       = ( float ) ( $deductionData['deductions'][0]->pagIbigEcl       ?? 0.00 );
            $empPagIbigMp2       = ( float ) ( $deductionData['deductions'][0]->pagIbigMp2       ?? 0.00 );
            $empNhmfc            = ( float ) ( $deductionData['deductions'][0]->nhmfc            ?? 0.00 );
            $empMaxicare         = ( float ) ( $deductionData['deductions'][0]->maxicare         ?? 0.00 );
            $empOtherBills       = ( float ) ( $deductionData['deductions'][0]->otherBills       ?? 0.00 );
            $empManualWhTax      = ( float ) ( $deductionData['deductions'][0]->manualWhTax      ?? 0.00 );
            $empAgeDeducBypass   = (  int  ) ( $deductionData['deductions'][0]->ageDeducBypass   ?? 0    );

            // Absences & Tardiness Related Declarations
            $atDays    = ( int ) ( $deductionData['deductions'][0]->atDays    ?? 0 );
            $atHours   = ( int ) ( $deductionData['deductions'][0]->atHours   ?? 0 );
            $atMinutes = ( int ) ( $deductionData['deductions'][0]->atMinutes ?? 0 );

            /* In this section, we compute for the amount to be deducted from the employees'
               salary based on the Absences & Tardiness incurred. */
            $atDeductionData    = $this->computationClass->atDeductions( 0, $empBaseSalary, $empPera, $atDays, $atHours, $atMinutes, 22, 0.00 );
            $atSalaryDeductions = $atDeductionData['atDeductions'];
            $atPeraDeductions   = $atDeductionData['peraDeductions'];

            // GSIS Life & Retirement Related Declarations
            $gsisData = $this->computationClass->gsisLr( $empBaseSalary, $atSalaryDeductions );
            $empGsisLr = ( float ) $gsisData['gsis_lr'];
            
            /* For employees 65 years old & above, they no longer have GSIS & PAG-IBIG Deductions,
               or they may choose to still have them, so remove these deductions from them if
               they want to. */
            if ( isset( $employeeAge ) && $employeeAge >= 65 && $empAgeDeducBypass == 0 ) {
                // GSIS Deductions
                $empGsisConsolidated = ( float ) 0.00;
                $empGsisEducation    = ( float ) 0.00;
                $empGsisEmergency    = ( float ) 0.00;
                $empGsisLr           = ( float ) 0.00;
                $empGsisPolicy       = ( float ) 0.00;
                $empGsisUmidCa       = ( float ) 0.00;
                $empGsisUoliLoan     = ( float ) 0.00;
                $empGsisUoliPolicy   = ( float ) 0.00;
                $empGsisGfal         = ( float ) 0.00;

                // PAG-IBIG Deductions
                $empPagIbig    = ( float ) 0.00;
                $empPagIbigEcl = ( float ) 0.00;
                $empPagIbigMpl = ( float ) 0.00;
                $empPagIbigMp2 = ( float ) 0.00;
            }

            $deductions = [];

            if ( $cutOffPeriod == 1 ) {
                $empGrossSalary          = $empPartialPayment + $empLvtPay;
                $deductions['empLvtPay'] = $empLvtPay; // included to $deductions array for dynamic saving
                $empNetSalary            = $empGrossSalary;
                
                if ( $empNetSalary - $empGsisLr >= 2500 ) {
                    $empNetSalary -= $empGsisLr;
                    $deductions['empGsisLr'] = $empGsisLr;
                }
                if ( $empNetSalary - $empPhilHealth_Ps >= 2500 ) {
                    $empNetSalary -= $empPhilHealth_Ps;
                    $deductions['empPhilHealth_Ps'] = $empPhilHealth_Ps;
                    $deductions['empPhilHealth_Es'] = $empPhilHealth_Es;
                }
                if ( $empNetSalary - $atSalaryDeductions >= 2500 ) {
                    $empNetSalary -= $atSalaryDeductions;
                    $deductions['atSalaryDeductions'] = $atSalaryDeductions;
                }
                if ( $empNetSalary - $empPagIbig >= 2500 ) {
                    $empNetSalary -= $empPagIbig;
                    $deductions['empPagIbig'] = $empPagIbig;
                }
                if ( $empNetSalary - $empLandBank >= 2500 ) {
                    $empNetSalary -= $empLandBank;
                    $deductions['empLandBank'] = $empLandBank;
                }
                if ( $empNetSalary - $empPlmPcci >= 2500 ) {
                    $empNetSalary -= $empPlmPcci;
                    $deductions['empPlmPcci'] = $empPlmPcci;
                }
                if ( $empNetSalary - $empPhilamLife >= 2500 ) {
                    $empNetSalary -= $empPhilamLife;
                    $deductions['empPhilamLife'] = $empPhilamLife;
                }
                if ( $empNetSalary - $empStudyGrant >= 2500 ) {
                    $empNetSalary -= $empStudyGrant;
                    $deductions['empStudyGrant'] = $empStudyGrant;
                }
                if ( $empNetSalary - $empGsisConsolidated >= 2500 ) {
                    $empNetSalary -= $empGsisConsolidated;
                    $deductions['empGsisConsolidated'] = $empGsisConsolidated;
                }
                if ( $empNetSalary - $empGsisEducation >= 2500 ) {
                    $empNetSalary -= $empGsisEducation;
                    $deductions['empGsisEducation'] = $empGsisEducation;
                }
                if ( $empNetSalary - $empGsisEmergency >= 2500 ) {
                    $empNetSalary -= $empGsisEmergency;
                    $deductions['empGsisEmergency'] = $empGsisEmergency;
                }
                if ( $empNetSalary - $empGsisPolicy >= 2500 ) {
                    $empNetSalary -= $empGsisPolicy;
                    $deductions['empGsisPolicy'] = $empGsisPolicy;
                }
                if ( $empNetSalary - $empGsisUmidCa >= 2500 ) {
                    $empNetSalary -= $empGsisUmidCa;
                    $deductions['empGsisUmidCa'] = $empGsisUmidCa;
                }
                if ( $empNetSalary - $empGsisUoliLoan >= 2500 ) {
                    $empNetSalary -= $empGsisUoliLoan;
                    $deductions['empGsisUoliLoan'] = $empGsisUoliLoan;
                }
                if ( $empNetSalary - $empGsisUoliPolicy >= 2500 ) {
                    $empNetSalary -= $empGsisUoliPolicy;
                    $deductions['empGsisUoliPolicy'] = $empGsisUoliPolicy;
                }
                if ( $empNetSalary - $empGsisGfal >= 2500 ) {
                    $empNetSalary -= $empGsisGfal;
                    $deductions['empGsisGfal'] = $empGsisGfal;
                }
                if ( $empNetSalary - $empPagIbigMpl >= 2500 ) {
                    $empNetSalary -= $empPagIbigMpl;
                    $deductions['empPagIbigMpl'] = $empPagIbigMpl;
                }
                if ( $empNetSalary - $empPagIbigEcl >= 2500 ) {
                    $empNetSalary -= $empPagIbigEcl;
                    $deductions['empPagIbigEcl'] = $empPagIbigEcl;
                }
                if ( $empNetSalary - $empPagIbigMp2 >= 2500 ) {
                    $empNetSalary -= $empPagIbigMp2;
                    $deductions['empPagIbigMp2'] = $empPagIbigMp2;
                }
                if ( $empNetSalary - $empNhmfc >= 2500 ) {
                    $empNetSalary -= $empNhmfc;
                    $deductions['empNhmfc'] = $empNhmfc;
                }
                if ( $empNetSalary - $empMaxicare >= 2500 ) {
                    $empNetSalary -= $empMaxicare;
                    $deductions['empMaxicare'] = $empMaxicare;
                }
                if ( $empNetSalary - $empOtherBills >= 2500 ) {
                    $empNetSalary -= $empOtherBills;
                    $deductions['empOtherBills'] = $empOtherBills;
                }
            } elseif ( $cutOffPeriod == 2 ) {
                $totalNoOfDays = $empNoOfDays;
                
                $epReportSummaryModel = new EPReportSummaryMdl();
                
                $firstQuincenaData = $epReportSummaryModel
                    ->where( 'earningYear', substr( $yearMonth, 0, 4 ) )
                    ->where( 'earningMonth', substr( $yearMonth, 5, 2 ) )
                    ->where( 'cutOffPeriod', 1 )
                    ->where( 'empNumber', $empNumber )
                    ->get();

                if ( count( $firstQuincenaData ) > 1 ) {
                    throw new \Exception('klkl;*');
                }

                $totalNoOfDays += isset( $firstQuincenaData[0] ) ? $firstQuincenaData[0]->empNoOfDays : 0;
                
                $totalAtDays    = $atDays + isset( $firstQuincenaData[0] ) ? $firstQuincenaData[0]->at_days : 0;
                $totalAtHours   = $atHours + isset( $firstQuincenaData[0] ) ? $firstQuincenaData[0]->at_hours : 0;
                $totalAtMinutes = $atMinutes + isset( $firstQuincenaData[0] ) ? $firstQuincenaData[0]->at_minutes : 0;

                /* In this section, we compute for the amount to be deducted from the employees'
                   salary based on the Absences & Tardiness incurred. */
                $atDeductionData  = $this->computationClass->atDeductions( 0, $empBaseSalary, $empPera, $totalAtDays, $totalAtHours, $totalAtMinutes, 22, 0.00 );
                $atPeraDeductions = $atDeductionData['peraDeductions'];

                $empPera -= $atPeraDeductions;
                $deductions['atPeraDeductions'] = $atPeraDeductions;
                $deductions['empPera']          = $empPera;

                $empGrossSalary = $empPartialPayment + $empPera;
                $empNetSalary   = $empGrossSalary;

                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_lr == 0 ) && $empNetSalary - $empGsisLr >= 2500 ) {
                    $empNetSalary -= $empGsisLr;
                    $deductions['empGsisLr'] = $empGsisLr;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_philHealth == 0 ) && $empNetSalary - $empPhilHealth_Ps >= 2500 ) {
                    $empNetSalary -= $empPhilHealth_Ps;
                    $deductions['empPhilHealth_Ps'] = $empPhilHealth_Ps;
                    $deductions['empPhilHealth_Es'] = $empPhilHealth_Es;
                }
                if ( $empNetSalary - $atSalaryDeductions >= 2500 ) {
                    $empNetSalary -= $atSalaryDeductions;
                    $deductions['atSalaryDeductions'] = $atSalaryDeductions;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->pi_premium == 0 ) && $empNetSalary - $empPagIbig >= 2500 ) {
                    $empNetSalary -= $empPagIbig;
                    $deductions['empPagIbig'] = $empPagIbig;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_landBank == 0 ) && $empNetSalary - $empLandBank >= 2500 ) {
                    $empNetSalary -= $empLandBank;
                    $deductions['empLandBank'] = $empLandBank;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_plmPcci == 0 ) && $empNetSalary - $empPlmPcci >= 2500 ) {
                    $empNetSalary -= $empPlmPcci;
                    $deductions['empPlmPcci'] = $empPhilamLife;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_philamLife == 0 ) && $empNetSalary - $empPhilamLife >= 2500 ) {
                    $empNetSalary -= $empPhilamLife;
                    $deductions['empPhilamLife'] = $empPhilamLife;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_studyGrant == 0 ) && $empNetSalary - $empStudyGrant >= 2500 ) {
                    $empNetSalary -= $empStudyGrant;
                    $deductions['empStudyGrant'] = $empStudyGrant;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_consolidated == 0 ) && $empNetSalary - $empGsisConsolidated >= 2500 ) {
                    $empNetSalary -= $empGsisConsolidated;
                    $deductions['empGsisConsolidated'] = $empGsisConsolidated;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_education == 0 ) && $empNetSalary - $empGsisEducation >= 2500 ) {
                    $empNetSalary -= $empGsisEducation;
                    $deductions['empGsisEducation'] = $empGsisEducation;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_emergency == 0 ) && $empNetSalary - $empGsisEmergency >= 2500 ) {
                    $empNetSalary -= $empGsisEmergency;
                    $deductions['empGsisEmergency'] = $empGsisEmergency;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_policy == 0 ) && $empNetSalary - $empGsisPolicy >= 2500 ) {
                    $empNetSalary -= $empGsisPolicy;
                    $deductions['empGsisPolicy'] = $empGsisPolicy;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_umidCa == 0 ) && $empNetSalary - $empGsisUmidCa >= 2500 ) {
                    $empNetSalary -= $empGsisUmidCa;
                    $deductions['empGsisUmidCa'] = $empGsisUmidCa;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_uoliLoan == 0 ) && $empNetSalary - $empGsisUoliLoan >= 2500 ) {
                    $empNetSalary -= $empGsisUoliLoan;
                    $deductions['empGsisUoliLoan'] = $empGsisUoliLoan;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_uoliPolicy == 0 ) && $empNetSalary - $empGsisUoliPolicy >= 2500 ) {
                    $empNetSalary -= $empGsisUoliPolicy;
                    $deductions['empGsisUoliPolicy'] = $empGsisUoliPolicy;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->gsis_gfal == 0 ) && $empNetSalary - $empGsisGfal >= 2500 ) {
                    $empNetSalary -= $empGsisGfal;
                    $deductions['empGsisGfal'] = $empGsisGfal;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->pi_mpl == 0 ) && $empNetSalary - $empPagIbigMpl >= 2500 ) {
                    $empNetSalary -= $empPagIbigMpl;
                    $deductions['empPagIbigMpl'] = $empPagIbigMpl;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->pi_ecl == 0 ) && $empNetSalary - $empPagIbigEcl >= 2500 ) {
                    $empNetSalary -= $empPagIbigEcl;
                    $deductions['empPagIbigEcl'] = $empPagIbigEcl;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->pi_mp2 == 0 ) && $empNetSalary - $empPagIbigMp2 >= 2500 ) {
                    $empNetSalary -= $empPagIbigMp2;
                    $deductions['empPagIbigMp2'] = $empPagIbigMp2;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_nhmfc == 0 ) && $empNetSalary - $empNhmfc >= 2500 ) {
                    $empNetSalary -= $empNhmfc;
                    $deductions['empNhmfc'] = $empNhmfc;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_maxicare == 0 ) && $empNetSalary - $empMaxicare >= 2500 ) {
                    $empNetSalary -= $empMaxicare;
                    $deductions['empMaxicare'] = $empMaxicare;
                }
                if ( ( ! isset ( $firstQuincenaData[0] ) || $firstQuincenaData[0]->ded_otherBills == 0 ) && $empNetSalary - $empOtherBills >= 2500 ) {
                    $empNetSalary -= $empOtherBills;
                    $deductions['empOtherBills'] = $empOtherBills;
                }
           
                // Taxable Salary Computation
                if ( $employeeAge >= 65 ) {
                    $empTaxableSalary = ( float ) (
                        $empBaseSalary      -
                        $atSalaryDeductions -
                        $empGsisLr          -
                        $empPhilHealth_Ps
                    );
                } else {
                    $empTaxableSalary = ( float ) (
                        $empBaseSalary      -
                        $atSalaryDeductions -
                        $empGsisLr          -
                        $empPhilHealth_Ps   -
                        env( 'TAXRATES_PAGIBIG_PREMIUM', 100 )
                    );
                }
                
                $empWhTax = $this->computationClass->withHoldingTax( $empTaxableSalary, 0, 0 );
                if ( $empNetSalary - $empWhTax >= 2500 ) {
                    $empNetSalary -= $empWhTax;
                    $deductions['empTaxableSalary'] = $empTaxableSalary;
                    $deductions['empWhTax'] = $empWhTax;
                }
            }

            return array_merge( [
                'empNumber'         => $employee->employeeNumber,
                'empName'           => $employee->fullName,
                'empDesignation'    => $employee->positionName,
                'empDepartment'     => $employee->departmentId,
                'empBaseSalary'     => round( $empBaseSalary, 2 ),
                'empNoOfDays'       => $empNoOfDays,
                'empPartialPayment' => round( $empPartialPayment, 2 ),
                'empGrossSalary'    => round( $empGrossSalary, 2 ),
                'empNetSalary'      => round( $empNetSalary, 2 ),
                'atDays'            => $atDays,
                'atHours'           => $atHours,
                'atMinutes'         => $atMinutes,
            ], $deductions );
        } catch( \PDOException $p ) {
            \Log::info($p);
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
            return response()-json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        } catch( \Throwable $t ) {
            \Log::info($t);
            throw new \Exception( $t->getMessage() );
        }
    }

    // Job Order Payroll
    public function jobOrderPayroll( int $jobOrderSection, array $excludedEmployees, string $earningYear,
                                     string $earningMonth ) : ?array {
        try {
            // The basics... :)
            $joEmployeeData = [];
            $reportId = $this->helperClass->makeOptimizedUuid();

            // Fetch the employees with Job Order Tenure & their data
            $jobOrderEmployees = $this->getJobOrderEmployees( $jobOrderSection, $excludedEmployees );

            // Get the number of Job Order Employees
            $joEmployeeCount = count( $jobOrderEmployees );

            // Checking routine if there are results returned
            if( $joEmployeeCount > 0 ) {
                for( $index = 0; $index < $joEmployeeCount; $index++ ) {
                    // Employee Information
                    $employeeNumber           = $jobOrderEmployees[$index]->employeeNumber;
                    $employeeName             = $jobOrderEmployees[$index]->fullName;
                    $employeeDesignation      = $jobOrderEmployees[$index]->positionName;
                    $employeeDailyRate        = $jobOrderEmployees[$index]->salaryAmount;
                    $employeeHourlyRate       = $employeeDailyRate / 8;
                    $isExcluded               = $jobOrderEmployees[$index]->isExcluded;
                    $daysWorked               = $jobOrderEmployees[$index]->daysWorked;
                    $ordinaryDayHours         = $jobOrderEmployees[$index]->ordinaryDayHours;
                    $restDayHours             = $jobOrderEmployees[$index]->restDayHours;
                    $specialHolidayHours      = $jobOrderEmployees[$index]->specialHolidayHours;
                    $rdAndSpecialHolidayHours = $jobOrderEmployees[$index]->rdAndSpecialHolidayHours;
                    $regularHolidayHours      = $jobOrderEmployees[$index]->regularHolidayHours;
                    $rhAndSpecialHolidayHours = $jobOrderEmployees[$index]->rhAndSpecialHolidayHours;
                    $doubleHolidayHours       = $jobOrderEmployees[$index]->doubleHolidayHours;
                    $rdAndDoubleHolidayHours  = $jobOrderEmployees[$index]->rdAndDoubleHolidayHours;
                    $cutOffEarnings           = $jobOrderEmployees[$index]->cutOffEarnings;
                    $otherEarnings            = $jobOrderEmployees[$index]->otherEarnings;
                    $pagIbigPremium           = $jobOrderEmployees[$index]->pagIbigPremium;
                    $atDays                   = $jobOrderEmployees[$index]->atDays;
                    $atHours                  = $jobOrderEmployees[$index]->atHours;
                    $atMinutes                = $jobOrderEmployees[$index]->atMinutes;

                    // Step 1 : Multiply Employee Daily Rate to Days Worked
                    $grossSalary = $employeeDailyRate * $daysWorked;

                    // Step 2A : Compute for the Ordinary Day Differential
                    $ordinaryDayDiff = $employeeHourlyRate * 0.1 * $ordinaryDayHours;

                    // Step 2B : Compute for the Rest Day Differential
                    $restDayDiff = $employeeHourlyRate * 1.3 * 0.1 * $restDayHours;

                    // Step 2C : Compute for the Special Holiday Differential
                    $specialHolidayDiff = $employeeHourlyRate * 1.3 * 0.1 * $specialHolidayHours;

                    // Step 2D : Compute for the Rest Day and Special Holiday Differential
                    $rdAndSpecialHolidayDiff = $employeeHourlyRate * 1.5 * 0.1 * $rdAndSpecialHolidayHours;

                    // Step 2E : Compute for the Regular Holiday Differential
                    $regularHolidayDiff = $employeeHourlyRate * 2.0 * 0.1 * $regularHolidayHours;

                    // Step 2F : Compute for the Rest Day and Regular Holiday Differential
                    $rhAndSpecialHolidayDiff = $employeeHourlyRate * 2.6 * 0.1 * $rhAndSpecialHolidayHours;

                    // Step 2G : Compute for the Double Holiday Differential
                    $doubleHolidayDiff = $employeeHourlyRate * 3.3 * 0.1 * $doubleHolidayHours;

                    // Step 2H : Compute for the Rest Day and Double Holiday Differential
                    $rdAndDoubleHolidayDiff = $employeeHourlyRate * 3.9 * 0.1 * $rdAndDoubleHolidayHours;

                    // Step 2I : Add all differential pay
                    $nightDifferential = (
                        $ordinaryDayDiff +
                        $restDayDiff +
                        $specialHolidayDiff +
                        $rdAndSpecialHolidayDiff +
                        $regularHolidayDiff +
                        $rhAndSpecialHolidayDiff +
                        $doubleHolidayDiff +
                        $rdAndDoubleHolidayDiff
                    );

                    // Step 2J : Compute for the adjusted gross salary
                    $grossSalary += $nightDifferential + $cutOffEarnings + $otherEarnings;

                    // Step 3 : Compute tardiness incurred
                    $atDaysAmount    = $employeeDailyRate * $atDays;
                    $atHoursAmount   = $employeeHourlyRate * $atHours;
                    $atMinutesAmount = ( $employeeHourlyRate / 60 ) * $atMinutes;
                    $atTotalAmount   = $atDaysAmount + $atHoursAmount + $atMinutesAmount;

                    // Step 4 : Deduct premiums & tardiness. The result is the Net Pay
                    $netSalary = $grossSalary - ( $pagIbigPremium + $atTotalAmount );

                    // Step 5 : Store computed values into array for writing to database
                    $joEmployeeData[$index] = [
                        'jrp_uid'             => $reportId,
                        'employeeName'        => $employeeName,
                        'employeeNumber'      => $employeeNumber,
                        'employeeDesignation' => $employeeDesignation,
                        'dailyRate'           => round( $employeeDailyRate, 2 ),
                        'daysWorked'          => $daysWorked,
                        'nightDifferential'   => $nightDifferential,
                        'grossPayAmt'         => round( $grossSalary, 2 ),
                        'netPayAmt'           => round( $netSalary, 2 ),
                        'otherEarnings'       => round( $otherEarnings, 2 ),
                        'cutOffEarnings'      => round( $cutOffEarnings, 2 ),
                        'pagIbigPremium'      => round( $pagIbigPremium, 2 ),
                        'tardiness'           => round( $atTotalAmount, 2 ),
                        'remarks'             => 'TEST',
                        'isExcluded'          => $isExcluded
                    ];
                }

                // Compact the parameters & data into an array before returning
                $returnValue = [
                    'parameters' => [
                        'uid'             => $reportId,
                        'jobOrderSection' => $jobOrderSection,
                        'reportYear'      => $earningYear,
                        'reportMonth'     => $earningMonth
                    ],
                    'data' => $joEmployeeData
                ];
            } else {
                $returnValue = null;
            }

            // Finally, return the value :)
            return $returnValue;
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }
    }

    // This is meant to act as a helper function to ease out migration
    private function getJobOrderEmployees( int $jobOrderSection, array $excludedEmployees = [] ) : Collection {
        try {
            // Refer to plm_hris_integrity.itg_departments table (31 = Janitorial, 56, 63, 64 = Security)
            $department = ( $jobOrderSection === 1 ? [31] : [56, 63, 64] );

            // Size of the excluded employees array
            $excludedCount = count( $excludedEmployees );

            // Dynamically built query to identify excluded employees
            $dynamicSql = function() use( $excludedCount ) : string {
                $paramString = '';
                if( $excludedCount > 0 ) {
                    for( $counter = 1; $counter <= $excludedCount; $counter++ ) {
                        $paramString .= '?,';
                    }
                    $paramString = substr_replace( $paramString, '', mb_strlen( $paramString ) - 1, 1 );
                    $sqlString = "ed.*, jed.*, ( CASE WHEN ed.employeeNumber IN ( {$paramString} ) THEN 1 ELSE 0 END ) AS isExcluded";
                } else {
                    $sqlString = "ed.*, jed.*, 0 AS isExcluded";
                }
                return $sqlString;
            };

            // Query the database using the dynamically built query
            $employeeDetailsModel = new EmployeeDetailsMdl();
            $joEmployeeDataModel  = new JoEmployeeDataMdl();

            $jobOrderEmployees = DB::table( "{$employeeDetailsModel->table} AS ed" )
                ->selectRaw( $dynamicSql(), [$excludedEmployees] )
                ->join( "{$joEmployeeDataModel->table} AS jed", 'ed.employeeNumber', 'jed.employeeNumber' )
                ->whereIn( 'departmentId', $department )
                ->where( 'employeeStatus', 1 ) // 1 = Active Employees
                ->where( 'tenure', 15 ) // Refer to plm_hris_integrity.itg_tenure table (15 = Job Order)
                ->where( 'fPaymentComplete', 1 ) // 1 = Yes
                ->get();

            // Finally, return the value
            return $jobOrderEmployees;
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }
    }
}
