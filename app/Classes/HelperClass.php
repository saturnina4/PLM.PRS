<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Classes;

use Illuminate\Support\Collection;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\SalaryTrancheScheduleModel;
use MiSAKACHi\VERACiTY\Http\Models\SignatoriesEffectivityModel;
use MiSAKACHi\VERACiTY\Interfaces\HelperInterface;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsMdl;
use Ramsey\Uuid\Uuid;

final class HelperClass implements HelperInterface {
    protected $deductionsModel,
              $departmentsModel,
              $employeeDetailsModel,
              $salaryTrancheModel,
              $signatoriesModel;

    public function __construct() {
        $this->deductionsModel      = new DeductionsMdl();
        $this->departmentsModel     = new DepartmentsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
        $this->salaryTrancheModel   = new SalaryTrancheScheduleModel();
        $this->signatoriesModel     = new SignatoriesEffectivityModel();
    }

    public function makeOptimizedUuid( bool $hex = true ) : string {
        $rawUuid = Uuid::uuid4()->toString();

        $optimizedUuid = ( string ) (
            substr( $rawUuid , 14, 4 ) .
            substr( $rawUuid , 9, 4 )  .
            substr( $rawUuid , 0, 8 )  .
            substr( $rawUuid , 19, 4 ) .
            substr( $rawUuid , 24)
        );

        if( ! $hex ) {
            $optimizedUuid = ( string ) ( hex2bin( $optimizedUuid ) );
        }

        return $optimizedUuid;
    }

    public function getEmployeeData( int $reportType, int $department, array $excludedEmployees = [] ) : Collection {
        $returnValue     = [
            'employees' => []
        ];

        $employeeData    = [];
        $excludedCount   = count( $excludedEmployees );
        $dynamicSql      = function() use( $excludedCount ) : string {
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

        try {
            // Get Tenure ID's from 'plm_hris_integrity.itg_tenure' table
            if( $reportType === 1 ) { // 1 = General Payroll - Regular
                $employeeData = $this->employeeDetailsModel
                    ->selectRaw( $dynamicSql(), [$excludedEmployees] )
                    ->where( 'departmentId', $department )
                    ->where( 'employeeStatus', 1 ) // 1 = Active Employees
                    ->whereIn( 'tenure', [1,3,5,6,12] ) // 1 = Permanent, Temporary, Terminous, Secondment & Co-Terminous // and part time
                    ->where( 'fPaymentComplete', 1 ) // 1 = Yes
                    ->get();
            } else if ( $reportType === 2 ) { // 2 = General Payroll - Casual
                $employeeData = $this->employeeDetailsModel
                    ->selectRaw( $dynamicSql(), [$excludedEmployees] )
                    ->where( 'departmentId', $department )
                    ->where( 'employeeStatus', 1 ) // 1 = Active Employees
                    ->where( 'tenure', 4 ) // 4 = Casual
                    ->where( 'fPaymentComplete', 1 ) // 1 = Yes
                    ->get();
            } else {
                dd( 'To be implemented' );
            }

            // Set the return value if there are results from the queries above
            if( count( $employeeData ) > 0 ) {
                $returnValue = [
                    'employees' => $employeeData
                ];
            }
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return collect( $returnValue );
    }

    public function getEmployeeDeductions( string $employeeNumber ) : Collection {
        $returnValue = [
            'deductions' => null
        ];

        try {
            $deductionsData = $this->deductionsModel
                ->where( 'empNumber', $employeeNumber )
                ->get();

            if( count( $deductionsData ) > 0 ) {
                $returnValue = [
                    'deductions' => $deductionsData
                ];
            }
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return collect( $returnValue );
    }

    public function getActiveTranche() : Collection {
        $returnValue = [
            'tranche' => null,
            'version' => null
        ];

        try {
            $activeTrancheData = $this->salaryTrancheModel
                ->get();

            if( count( $activeTrancheData ) > 0 ) {
                $returnValue = [
                    'tranche' => $activeTrancheData[0]->activeTranche,
                    'version' => $activeTrancheData[0]->activeVersion
                ];
            }
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return collect( $returnValue );
    }

    public function getSignatories( string $earningPeriod ) : Collection {
        $returnValue = [
            'signatories' => null
        ];

        try {
            $signatoriesData = $this->signatoriesModel
                ->whereRaw( "? BETWEEN effectivityDateFrom AND effectivityDateTo", [$earningPeriod] )
                ->get();

            if( count( $signatoriesData ) > 0 ) {
                $returnValue = [
                    'signatories' => $signatoriesData[0]->id
                ];
            }
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return collect( $returnValue );
    }

    public function getDepartmentDetails( int $departmentId ) : Collection {
        $returnValue = [
            'name'          => ( string ) 'N/A',
            'code'          => ( string ) 'N/A',
            'deptHead'      => ( string ) 'N/A',
            'deptHeadTitle' => ( string ) 'N/A'
        ];

        try {
            $departmentsData = $this->departmentsModel
                ->where( 'id', $departmentId )
                ->get();

            if( count( $departmentsData ) > 0 ) {
                $returnValue = [
                    'name'          => $departmentsData[0]['deptname'],
                    'code'          => $departmentsData[0]['deptcode'],
                    'deptHead'      => $departmentsData[0]['deptHead'],
                    'deptHeadTitle' => $departmentsData[0]['deptHeadTitle']
                ];
            }
        } catch( \PDOException $p ) {
            throw new \Exception( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return collect( $returnValue );
    }

    public function emptyInputFilter( string $value, bool $nullable = false ) {
        $value = trim( $value );

        if( $value !== '' ) {
            return $value;
        } else {
            if( $nullable ) {
                return null;
            } else {
                return '';
            }
        }
    }
}
