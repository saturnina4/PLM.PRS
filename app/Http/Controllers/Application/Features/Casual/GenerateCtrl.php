<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\Casual;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JSONResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Classes\PayrollClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\CPReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\CPReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PartTimeEmpDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\CPGenerateRqst;
use MiSAKACHi\VERACiTY\Traits\GeneratesUuidTrait;

final class GenerateCtrl extends Controller {
    use GeneratesUuidTrait;

    protected $departmentsModel,
              $employeeDetailsModel,
              $gpReportDataModel,
              $gpReportParamsModel,
              $helperClass,
              $payrollClass;

    public function __construct() {
        $this->departmentsModel     = new DepartmentsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
        $this->cpReportDataModel    = new CPReportDataMdl();
        $this->cpReportParamsModel  = new CPReportParamsMdl();
        $this->helperClass          = new HelperClass();
        $this->payrollClass         = new PayrollClass();
    }

    public function getAction() : View {
        $empList = $this->employeeDetailsModel
            ->join("{$this->departmentsModel->table}", "{$this->employeeDetailsModel->table}.departmentId", "{$this->departmentsModel->table}.id")
            ->where( 'tenure', 4 )
            ->where( 'employeeStatus', 1 )
            ->where( 'fPaymentComplete', 1 )
            ->whereNotNull( 'salaryValue' )
            ->whereNotNull( 'employeeNumber' )
            ->orderBy( 'deptname', 'asc' )
            ->orderBy( 'fullName', 'asc' )
            ->get();

        return view( 'Application.Features.Casual.GeneratorView' )
            ->with( 'empList', $empList );
    }

    public function postAction( CPGenerateRqst $request ) : JSONResponse {
        try {
            $empDepartment = ( int ) $request->input( 'selectedDepartment' );
            $cutOffPeriod  = ( int ) $request->input( 'cutOffPeriod' );
            $earningYear   = ( string ) substr( $request->input( 'paymentDate' ), 0, 4 );
            $earningMonth  = ( string ) substr( $request->input( 'paymentDate' ), 5, 2 );
            $inclEmployees = [];
            $inclEmpId     = ( array ) ( $request->input( 'empId' ) ?? [] );
            $inclNoOfDays  = ( array ) ( $request->input( 'noOfDays' ) ?? [] );
            
            if ( ! empty( $inclEmpId ) ) {
                foreach ( $inclEmpId as $key => $value ) {
                    if ($inclNoOfDays[ $key ] != '') {
                        $inclEmployees[] = [
                            'empId'    => $value,
                            'noOfDays' => $inclNoOfDays[ $key ],
                        ];
                    }
                }
            }
            // dd($inclEmployees);
            $includedEmployees = $inclEmployees;

            $tempIncludedEmployees = $includedEmployees;
            foreach ($tempIncludedEmployees as $key => $value) {
                unset(
                    $tempIncludedEmployees[$key]['noOfDays']
                );
            }
            $tempIncludedEmployees = array_map( 'unserialize', array_unique( array_map( 'serialize', $tempIncludedEmployees) ) );

            if ( count( $includedEmployees ) == count( $tempIncludedEmployees ) ) {
                // Generate the report
                $payrollData = $this->payrollClass
                    ->casualPayroll( $cutOffPeriod, $earningYear, $earningMonth, $includedEmployees );

                if ( is_string( $payrollData ) ) {
                    return response()->json( [ 'ajaxFailure' => $payrollData ] );
                }

                if( count( $payrollData['data'] ) > 0 ) {
                    $cpRegularParams = [
                        'unique_id'          => $payrollData['params']['report_id'],
                        'cutOffPeriod'         => $cutOffPeriod,
                        'earningYear'        => $earningYear,
                        'earningMonth'       => $earningMonth,
                        'generatedBy'        => $request->session()->get( 'activeUser' )
                    ];

                    // Check if report is already generated
                    $checkIfReportExists = $this->cpReportParamsModel
                        ->where( 'cutOffPeriod', $cutOffPeriod )
                        ->where( 'earningYear', $earningYear )
                        ->where( 'earningMonth', $earningMonth )
                        ->get();

                    // Write values to database
                    if( isset( $checkIfReportExists[0] ) ) {
                        return response()->json( [ 'errorMessage' => 'A report was already generated for the selected period.' ] );
                    } else {
                        DB::transaction( function() use( $cpRegularParams, $payrollData ) {
                            DB::table( $this->cpReportParamsModel->table )
                                ->insert( $cpRegularParams );

                            DB::table( $this->cpReportDataModel->table )
                                ->insert( $payrollData['data'] );
                        });

                        return response()->json( [
                            'recordId' => $cpRegularParams['unique_id']
                        ]);
                    }
                } else {
                    return response()->json( [ 'ajaxFailure' => 'No employees found in the specified college.' ] );
                }
            } else {
                return response()->json( [ 'ajaxFailure' => 'It seems you have entries with the same months.' ] );
            }

        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
            return response()->json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        } catch( \Exception $t ) {
            // \Log::info($t);
            // throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
            return response()->json( [ 'ajaxFailure' => 'An error has occurred.' ] );
        }

        return redirect()->route( 'getPTGenReport' );
    }

    public function postFetchEmployees( Request $request ) {
        try {
            $deptId = $request->input( 'deptId' );
            $empData = [];

            $empData = $this->partTimeEmpDetailsModel
                ->where( 'departmentId', $deptId )
                ->orderBy( 'fullName', 'asc' )
                ->get();

            return response()->json([
                'empData' => $empData
            ]);
        } catch( \PDOException $e ) {
            throw new \PDOException( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Database]'] );
        } catch( \Throwable $e ) {
            throw new \Exception( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Application]'] );
        }
    }
}
