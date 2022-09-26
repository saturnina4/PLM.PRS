<?php

namespace MiSAKACHi\VERACiTY\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\GPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\SentPayslipsMdl;
use MiSAKACHi\VERACiTY\Mail\PayslipMailable;
use MiSAKACHi\VERACiTY\UDF\CommonFunctions;
use MiSAKACHi\VERACiTY\Traits\GeneratesUuidTrait;

define( 'UID', 'administrator@plmaynila.local' );
define( 'PASWD', 'PLM<@>$+3RP@ssword-1' );
define( 'SERVICE_URL', 'https://reportserver.icto.local/svc/' );

final class PayslipController extends Controller {
    use GeneratesUuidTrait;

    protected $departmentsModel;
    protected $employeeDetailsModel;

    public function __construct() {
        $this->departmentsModel     = new DepartmentsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
    }

    public function getAction(Request $request) {
        $adminDeptsList = $this->departmentsModel
            ->where( 'deptType', '1' )
            ->get();

        $acadDeptsList  = $this->departmentsModel
            ->where( 'deptType', '2' )
            ->get();

        $employeesList = $this->employeeDetailsModel
            ->where( 'employeeStatus', '1' )
            ->orderBy( 'fullName' )
            ->get();

        return view( 'Application.Features.Payslip.Main' )
            ->with( 'acadDeptsList', $acadDeptsList )
            ->with( 'adminDeptsList', $adminDeptsList )
            ->with( 'employeesList', $employeesList );
    }

    public function postAction(Request $request)
    {
        $this->validate($request, [
            'yearAndMonth' => 'required|size:7',
            'selectedDepartment' => 'required_without:selectedEmployee',
            'selectedEmployee' => 'required_without:selectedDepartment'
        ]);
        
        try {
            set_time_limit( 0 );
            $yearAndMonth = $request->input('yearAndMonth');
            $year         = substr($yearAndMonth, 0, 4);
            $month        = substr($yearAndMonth, 5, 2);
            $department   = $request->input('selectedDepartment');
            $employee     = $request->input('selectedEmployee');

            $gpReportSummaryMdl = new GPReportSummaryMdl();

            $selection = [];
            if (! empty($department)) {
                $selection = [
                    'selectedParam' => 'department',
                    'selectedValue' => $department
                ];
            } elseif (! empty($employee)) {
                $selection = [
                    'selectedParam' => 'empNumber',
                    'selectedValue' => $employee
                ];
            }

            $reportData = $gpReportSummaryMdl
                ->where([
                    ['earningYear', $year], // Fixed, make dynamic
                    ['earningMonth', $month],
                    [$selection[ 'selectedParam' ], $selection[ 'selectedValue' ]]
                ])
                ->orderBy( 'empName', 'asc' )
                ->get();

            $recepientCount = count( $reportData );
            $paySlipData    = [];
            $index          = 0;

            foreach( $reportData as $record ) {
                $formattedPayPeriodFrom = date_format( date_create( $record->payPeriodFrom ), 'F d, Y' );
                $formattedPayPeriodTo   = date_format( date_create( $record->payPeriodTo ), 'F d, Y' );

                // Employee Deductions
                $totalDeductions = (
                    $record->tax_whTax +
                    $record->gsis_total +
                    $record->pi_total +
                    $record->ded_landBank +
                    $record->ded_philHealth +
                    $record->ded_plmPcci +
                    $record->ded_philamLife +
                    $record->ded_studyGrant +
                    $record->ded_nhmfc +
                    $record->ded_maxicare +
                    $record->ded_otherBills +
                    $record->at_salaryDeductions +
                    $record->at_peraDeductions
                );
                $firstQuincena  = round( round( $record->empNetSalary , 0 ) / 2, 0 );
                $secondQuincena = $record->empNetSalary - $firstQuincena;

                $paySlipData[$index] = [
                    'employeeNumber'       => $record->empNumber,
                    'employeeName'         => $record->empName,
                    'empDesignation'       => $record->empDesignation,
                    'departmentName'       => $record->departmentName,
                    'department'           => $record->department,
                    'officialEmail'        => $record->officialEmail,
                    'payPeriod'            => $formattedPayPeriodFrom . ' to ' . $formattedPayPeriodTo,
                    'isExcluded'           => $record->isExcluded,
                    'empBaseSalary'        => CommonFunctions::zeroValueFilter( $record->empBaseSalary ),
                    'empLvtPay'            => CommonFunctions::zeroValueFilter( $record->empLvtPay ),
                    'empHazardPay'         => CommonFunctions::zeroValueFilter( $record->empHazardPay ),
                    'empPera'              => CommonFunctions::zeroValueFilter( $record->empPera ),
                    'tax_whTax'            => CommonFunctions::zeroValueFilter( $record->tax_whTax ),
                    'ded_philHealth'       => CommonFunctions::zeroValueFilter( $record->ded_philHealth ),
                    'ded_plmPcci'          => CommonFunctions::zeroValueFilter( $record->ded_plmPcci ),
                    'ded_philamLife'       => CommonFunctions::zeroValueFilter( $record->ded_philamLife ),
                    'ded_studyGrant'       => CommonFunctions::zeroValueFilter( $record->ded_studyGrant ),
                    'ded_nhmfc'            => CommonFunctions::zeroValueFilter( $record->ded_nhmfc ),
                    'ded_maxicare'         => CommonFunctions::zeroValueFilter( $record->ded_maxicare ),
                    'ded_landBank'         => CommonFunctions::zeroValueFilter( $record->ded_landBank ),
                    'ded_otherBills'       => CommonFunctions::zeroValueFilter( $record->ded_otherBills ),
                    'gsis_lr'              => CommonFunctions::zeroValueFilter( $record->gsis_lr ),
                    'gsis_policy'          => CommonFunctions::zeroValueFilter( $record->gsis_policy ),
                    'gsis_consolidated'    => CommonFunctions::zeroValueFilter( $record->gsis_consolidated ),
                    'gsis_emergency'       => CommonFunctions::zeroValueFilter( $record->gsis_emergency ),
                    'gsis_umidCa'          => CommonFunctions::zeroValueFilter( $record->gsis_umidCa ),
                    'gsis_uoliPolicy'      => CommonFunctions::zeroValueFilter( $record->gsis_uoliPolicy ),
                    'gsis_uoliLoan'        => CommonFunctions::zeroValueFilter( $record->gsis_uoliLoan ),
                    'gsis_education'       => CommonFunctions::zeroValueFilter( $record->gsis_education ),
                    'gsis_gfal'            => CommonFunctions::zeroValueFilter( $record->gsis_gfal ),
                    'gsis_mpl'             => CommonFunctions::zeroValueFilter( $record->gsis_mpl ),
                    'gsis_computerLoan'    => CommonFunctions::zeroValueFilter( $record->gsis_computerLoan ),
                    'pi_premium'           => CommonFunctions::zeroValueFilter( $record->pi_premium ),
                    'pi_ecl'               => CommonFunctions::zeroValueFilter( $record->pi_ecl ),
                    'pi_mpl'               => CommonFunctions::zeroValueFilter( $record->pi_mpl ),
                    'pi_mp2'               => CommonFunctions::zeroValueFilter( $record->pi_mp2 ),
                    'at_salaryDeductions'  => CommonFunctions::zeroValueFilter( $record->at_salaryDeductions ),
                    'at_peraDeductions'    => CommonFunctions::zeroValueFilter( $record->at_peraDeductions ),
                    'totalEarnings'        => number_format( $record->empBaseSalary + $record->empPera + $record->empLvtPay + $record->empHazardPay , 2 ),
                    'totalDeductions'      => number_format( $totalDeductions, 2 ),
                    'empNetSalary'         => number_format( $record->empNetSalary, 2 ),
                    'firstQuincena'        => number_format( $firstQuincena, 2 ),
                    'secondQuincena'       => number_format( $secondQuincena, 2 )
                ];

                $index++;
            }

            // SSRS Report Parameter Configuration
            $options = [
                'username' => UID,
                'password' => PASWD
            ];

            $ssrs = new \SSRS\Report( SERVICE_URL, $options );
            $result = $ssrs->loadReport( '/Payroll/GP_Regular_Payslip' );
            
            // Start queueing the data then send :)
            if( $recepientCount > 0 ) {
                for( $index = 0; $index < $recepientCount; $index++ ) {
                    if( $paySlipData[$index]['isExcluded'] === 0 ) {

                        $ssrs->setSessionId( $result->executionInfo->ExecutionID );
                        $ssrs->setExecutionParameters([
                            'Parameter1' => $paySlipData[$index]['employeeNumber'],
                            'Parameter2' => $year,
                            'Parameter3' => $month
                        ]);

                        $output = $ssrs->render( 'PDF' ); // PDF | XML | CSV | HTML4.0

                        $storagePath = 'C:\$SERVER\temp\mail\\';
                        $fileName = $this->makeOptimizedUuid() . '.pdf';
                        $finalPath = $storagePath . $fileName;
                        $outputFile = fopen( $finalPath, 'w' );
                        fwrite( $outputFile, $output );

                        Mail::to( $paySlipData[$index]['officialEmail'] )
                            ->queue( new PayslipMailable( $paySlipData[$index], $finalPath ) );
                            
                        $sentPayslip = new SentPayslipsMdl;
                        $sentPayslip->empNumber     = $paySlipData[$index]['employeeNumber'];
                        $sentPayslip->earningYear   = $year;
                        $sentPayslip->earningMonth  = $month;
                        $sentPayslip->department_id = $paySlipData[$index]['department'];
                        $sentPayslip->user_id       = session( 'activeUserId' );
                        $sentPayslip->save();
                    }
                }

                $request->session()->flash( 'successMessage', 'Payslips were sent successfully' );
            } else {
                $request->session()->flash( 'errorMessage', 'No payslips were sent' );
            }
        } catch( \PDOException $p ) {
            info($p);
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            info($t);
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return redirect()->route( 'getPayslip' );
    }
}
