<?php

namespace MiSAKACHi\VERACiTY\UDF;

use Illuminate\Database\Eloquent\Collection;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsModel;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsModel;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsModel;
use MiSAKACHi\VERACiTY\Http\Models\GsisMultiplierModel;
use MiSAKACHi\VERACiTY\Http\Models\PhilHealthContributionModel;
use MiSAKACHi\VERACiTY\Http\Models\WithHoldingTaxModel;

class CommonFunctions {
    public static function optimizeUuid( string $rawUuid ) : string {
        $optimizedUuid  = hex2bin(
            substr( $rawUuid , 14, 4 ) .
            substr( $rawUuid , 9, 4 )  .
            substr( $rawUuid , 0, 8 )  .
            substr( $rawUuid , 19, 4 ) .
            substr( $rawUuid , 24)
        );

        return $optimizedUuid;
    }

    /**
     * @param Int $departmentId     : The Department ID
     * @return string               : Name of the Department
     */
    public static function getDepartmentName( Int $departmentId ) : string {
        $departmentsModel = new DepartmentsModel;

        $departments = $departmentsModel
            ->where( 'id', $departmentId )
            ->get();

        return $departments[0]['deptname'] ?? '(N/A)';
    }

    /**
     * @param $value            : The value to be filtered
     * @return null | string    : Return value
     */
    public static function zeroValueFilter( $value ) {
        $value = trim( $value );
        $value = ( float ) $value;

        if( ! empty( $value ) ) {
            return number_format( $value, 2 );
        } else {
            return null;
        }
    }

    /**
     * @param float $baseSalary
     * @return float
     * @throws \Exception
     */
    public static function computeGsisLr( float $baseSalary ) : float {
        $gsisMultiplierModel = new GsisMultiplierModel();

        try {
            $gsisMultiplier = $gsisMultiplierModel
                ->where( 'share_type', 1 )
                ->get();

            if( count( $gsisMultiplier ) > 0 ) {
                return ( $baseSalary * ( $gsisMultiplier[0]->life_contrib + $gsisMultiplier[0]->retirement_contrib ) );
            }
        } catch( \Throwable $t ) {
            throw new \Exception( "Something went wrong while computing the GSIS Life & Retirement. [Error Code: {$t->getCode()}]" );
        }

        return 0.00;
    }

    /**
     * @param float $baseSalary
     * @return float
     * @throws \Exception
     */
    public static function computePhilHealth( float $baseSalary ) : float {
        $philHealthContributionModel = new PhilHealthContributionModel();

        try {
            $philHealthContrib = $philHealthContributionModel
                ->where( 'salary_base', '<=', $baseSalary )
                ->max( 'employee_share' );

            if( ! empty( $philHealthContrib ) ) {
                return $philHealthContrib;
            }
        } catch( \Throwable $t ) {
            throw new \Exception( "Something went wrong while computing the PhilHealth Contribution. [Error Code: {$t->getCode()}]" );
        }

        return 0.00;
    }

    /**
     * @param int $employeeType
     * @param int $department
     * @param int $employeeStatus
     * @param int $excludedCount
     * @param array $excludedEmployees
     * @return Collection
     * @throws \Exception
     */
    public static function getEmployeeDetails( int $employeeType, int $department, int $employeeStatus, int $excludedCount = 0, array $excludedEmployees = [] ) : Collection {
        $employeeDetailsModel = new EmployeeDetailsModel();

        try {
            $dynamicSql = function() use( $excludedCount ) : string {
                $paramString = '';
                if( $excludedCount > 0 ) {
                    for( $counter = 1; $counter <= $excludedCount; $counter++ ) {
                        $paramString .= '?,';
                    }
                    $paramString = substr_replace( $paramString, '', mb_strlen( $paramString ) - 1, 1 );
                    $sqlString   = "*, ( CASE WHEN employeeNumber IN ( {$paramString} ) THEN 1 ELSE 0 END ) AS isExcluded";
                } else {
                    $sqlString   = "*, 0 AS isExcluded";
                }
                return $sqlString;
            };

            if( $employeeType === 1 || $employeeType === 3 ) {
                $employeeDetails = $employeeDetailsModel
                    ->selectRaw( $dynamicSql(), [$excludedEmployees] )
                    ->where( 'departmentId', $department )
                    ->where( 'employeeStatus', $employeeStatus )
                    ->where( 'tenure', $employeeType )
                    ->where( 'fPaymentComplete', 1 ) // Employee must have received first payment before he/she is included in report
                    ->get();

                if( count( $employeeDetails ) ) {
                    return $employeeDetails;
                }
            } else {
                dd( 'To be implemented' );
            }
        } catch( \Throwable $t ) {
            throw new \Exception( "An error occurred while fetching employee's details. [Error Code: {$t->getCode()}]" );
        }

        return new Collection();
    }

    /**
     * @param string $employeeNumber
     * @param string $earningYear
     * @param int $earningMonth
     * @param int $earningPeriod
     * @return Collection
     * @throws \Exception
     */
    public static function getEmployeeDeductions( string $employeeNumber, string $earningYear, int $earningMonth, int $earningPeriod ) : Collection {
        $deductionsModel = new DeductionsModel();

        try {
            $deductions = $deductionsModel
                ->where( 'empNumber', $employeeNumber )
                ->where( 'deductionYear', $earningYear )
                ->where( 'deductionMonth', $earningMonth )
                ->where( 'deductionPeriod', $earningPeriod )
                ->get();

            if( count( $deductions ) > 0 ) {
                return $deductions;
            }
        } catch( \Throwable $t ) {
            throw new \Exception( "An error occurred while fetching employee's deductions. [Error Code: {$t->getCode()}]" );
        }

        return new Collection();
    }

    /**
     * Compute the Withholding Tax
     *
     * @param int $taxStatus
     * @param int $dependents
     * @param float $taxableSalary
     * @return float
     * @throws \Exception
     */
    public static function computeWhTax( int $taxStatus, int $dependents, float $taxableSalary ) : float {
        $withHoldingTaxModel = new WithHoldingTaxModel();

        try {
            $whTax = $withHoldingTaxModel
                ->selectRaw( "MAX( taxDefault ) AS 'taxDefault', MAX( taxPercentage ) AS 'taxPercentage', MAX( salaryAmount ) AS salaryAmount" )
                ->where( 'cutOffId', env( 'TAXRATES_TAX_TABLE_CUTOFF', 4 ) )
                ->where( 'taxStatus', $taxStatus )
                ->where( 'dependents', $dependents )
                ->where( 'salaryAmount', '<=', $taxableSalary )
                ->get();

            if( count( $whTax ) > 0 ) {
                return ( ( $taxableSalary - $whTax[0]->salaryAmount ) * $whTax[0]->taxPercentage ) + $whTax[0]->taxDefault;
            }
        } catch( \Throwable $t ) {
            throw new \Exception( "An error occurred while computing the withholding tax. [Error Code: {$t->getCode()}]" );
        }

        return 0.00;
    }

    public static function computeTardinessDeductions( int $positionType, float $baseSalary, float $peraAllowance, int $daysAbsent = 0, int $hoursLate = 0, int $minutesLate = 0, int $units = 0 ) : array {
        try {
            if( $positionType === 1 ) {
                $absenceDeductions         = ( $baseSalary / $units ) * $daysAbsent;
                $hoursLateDeductions       = ( ( $baseSalary / $units ) / 8 ) * $hoursLate;
                $minutesLateDeductions     = ( ( ( $baseSalary / $units ) / 8 ) / 60 ) * $minutesLate;
                $tardinessDeductions       = $absenceDeductions + $hoursLateDeductions + $minutesLateDeductions;
                $peraAbsenceDeductions     = ( $peraAllowance / $units ) * $daysAbsent;
                $peraHoursLateDeductions   = ( ( $peraAllowance / $units ) / 8 ) * $hoursLate;
                $peraMinutesLateDeductions = ( ( ( $peraAllowance / $units ) / 8 ) / 60 ) * $minutesLate;
                $peraDeductions            = $peraAbsenceDeductions + $peraHoursLateDeductions + $peraMinutesLateDeductions;

                return [
                    'tardinessDeductions' => $tardinessDeductions,
                    'peraDeductions'      => $peraDeductions
                ];
            } else if( $positionType === 2 ) {
                $absenceDeductions         = ( $baseSalary / 22 ) * $daysAbsent;
                $hoursLateDeductions       = ( ( $baseSalary / 22 ) / 8 ) * $hoursLate;
                $minutesLateDeductions     = ( ( ( $baseSalary / 22 ) / 8 ) / 60 ) * $minutesLate;
                $tardinessDeductions       = $absenceDeductions + $hoursLateDeductions + $minutesLateDeductions;
                $peraAbsenceDeductions     = ( $peraAllowance / 22 ) * $daysAbsent;
                $peraHoursLateDeductions   = ( ( $peraAllowance / 22 ) / 8 ) * $hoursLate;
                $peraMinutesLateDeductions = ( ( ( $peraAllowance / 22 ) / 8 ) / 60 ) * $minutesLate;
                $peraDeductions            = $peraAbsenceDeductions + $peraHoursLateDeductions + $peraMinutesLateDeductions; 
                
                return [
                    'tardinessDeductions' => $tardinessDeductions,
                    'peraDeductions'      => $peraDeductions
                ];
            }
        } catch ( \Throwable $t ) {
            throw new \Exception( "An error occurred while computing the absence deductions [Error Code: {$t->getCode()}]" );
        }

        return [];
    }
}
