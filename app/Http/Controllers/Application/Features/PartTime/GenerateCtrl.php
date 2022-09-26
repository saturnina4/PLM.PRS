<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\PartTime;

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
use MiSAKACHi\VERACiTY\Http\Models\PTReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PartTimeEmpDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\PTGenerateRqst;
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
        $this->partTimeEmpDetailsModel = new PartTimeEmpDetailsMdl();
        $this->ptReportDataModel    = new PTReportDataMdl();
        $this->ptReportParamsModel  = new PTReportParamsMdl();
        $this->helperClass          = new HelperClass();
        $this->payrollClass         = new PayrollClass();
    }

    public function getAction() : View {
        $acadDeptsList = $this->departmentsModel
            ->where( 'deptType', '2' )
            ->get();

        $ptEmpList = $this->partTimeEmpDetailsModel
            ->orderBy( 'fullName', 'asc' )
            ->get();

        return view( 'Application.Features.PartTime.GeneratorView' )
            ->with( 'acadDeptsList', $acadDeptsList )
            ->with( 'ptEmpList', $ptEmpList );
    }

    public function postAction( PTGenerateRqst $request ) : JSONResponse {
        try {
            $empDepartment        = ( int ) $request->input( 'selectedDepartment' );
            $earningYear          = ( string ) substr( $request->input( 'paymentDate' ), 0, 4 );
            $earningMonth         = ( string ) substr( $request->input( 'paymentDate' ), 5, 2 );
            $inclEmployees        = [];
            $inclEmpId            = ( array ) ( $request->input( 'empId' ) ?? [] );
            $inclNoOfHrs          = ( array ) ( $request->input( 'noOfHrs' ) ?? [] );
            $inclTaxPercent      = ( array ) ( $request->input( 'taxPercent' ) ?? [] );
            $inclOtherDeduc       = ( array ) ( $request->input( 'otherDeduc' ) ?? [] );
            $inclRemarks          = ( array ) ( $request->input( 'remarks' ) ?? [] );
            $prevExclEmployees    = [];
            $prevExclEmpId        = ( array ) ( $request->input( 'empInc' ) ?? [] );
            $prevExclNoOfHrs      = ( array ) ( $request->input( 'incNoOfHrs' ) ?? [] );
            $prevExclTaxPercent  = ( array ) ( $request->input( 'incTaxPercent' ) ?? [] );
            $prevExclOtherDeduc   = ( array ) ( $request->input( 'incOtherDeduc' ) ?? [] );
            $prevExclYearMonth    = ( array ) ( $request->input( 'incYearMonth' ) ?? [] );
            $prevExclRemarks      = ( array ) ( $request->input( 'incRemarks' ) ?? [] );
            $otherCollEmployees   = [];
            $otherCollEmpId       = ( array ) ( $request->input( 'empOther' ) ?? [] );
            $otherCollNoOfHrs     = ( array ) ( $request->input( 'otherNoOfHrs' ) ?? [] );
            $otherCollTaxPercent = ( array ) ( $request->input( 'otherTaxPercent' ) ?? [] );
            $otherCollOtherDeduc  = ( array ) ( $request->input( 'otherOtherDeduc' ) ?? [] );
            $otherCollYearMonth   = ( array ) ( $request->input( 'otherYearMonth' ) ?? [] );
            $otherCollRemarks     = ( array ) ( $request->input( 'otherRemarks' ) ?? [] );

            if ( ! empty( $inclEmpId ) ) {
                foreach ( $inclEmpId as $key => $value ) {
                    // $inclEmployees[] = [
                        // 'empId'       => $value,
                        // 'noOfHrs'     => $inclNoOfHrs[ $key ],
                        // 'taxPercent' => $inclTaxPercent[ $key ],
                        // 'otherDeduc'  => $inclOtherDeduc[ $key ],
                        // 'yearMonth'   => $request->input( 'paymentDate' ),
                        // 'remarks'     => $inclRemarks[ $key ]
                    // ];
                    if ($inclNoOfHrs[ $key ] != '') {
                        $inclEmployees[] = [
                            'empId'       => $value,
                            'noOfHrs'     => $inclNoOfHrs[ $key ],
                            'taxPercent' => $inclTaxPercent[ $key ],
                            'otherDeduc'  => $inclOtherDeduc[ $key ],
                            'yearMonth'   => $request->input( 'paymentDate' ),
                            'remarks'     => $inclRemarks[ $key ]
                        ];
                    }
                }
            }

            if ( ! empty( $prevExclEmpId ) ) {
                foreach ( $prevExclEmpId as $key => $value ) {
                    $prevExclEmployees[] = [
                        'empId'       => $value,
                        'noOfHrs'     => $prevExclNoOfHrs[ $key ],
                        'taxPercent' => $prevExclTaxPercent[ $key ],
                        'otherDeduc'  => $prevExclOtherDeduc[ $key ],
                        'yearMonth'   => $prevExclYearMonth[ $key ],
                        'remarks'     => $prevExclRemarks[ $key ]
                    ];
                }
            }

            if ( ! empty( $otherCollEmpId ) ) {
                foreach ( $otherCollEmpId as $key => $value ) {
                    $otherCollEmployees[] = [
                        'empId'       => $value,
                        'noOfHrs'     => $otherCollNoOfHrs[ $key ],
                        'taxPercent' => $otherCollTaxPercent[ $key ],
                        'otherDeduc'  => $otherCollOtherDeduc[ $key ],
                        'yearMonth'   => $otherCollYearMonth[ $key ] != '' ? $otherCollYearMonth[ $key ] : $request->input( 'paymentDate' ),
                        'remarks'     => $otherCollRemarks[ $key ]
                    ];
                }
            }

            $includedEmployees = array_merge( $inclEmployees, $prevExclEmployees, $otherCollEmployees );

            $tempIncludedEmployees = $includedEmployees;
            foreach ($tempIncludedEmployees as $key => $value) {
                unset(
                    $tempIncludedEmployees[$key]['noOfHrs'],
                    $tempIncludedEmployees[$key]['taxPercent'],
                    $tempIncludedEmployees[$key]['otherDeduc'],
                    $tempIncludedEmployees[$key]['remarks']
                );
            }
            $tempIncludedEmployees = array_map( 'unserialize', array_unique( array_map( 'serialize', $tempIncludedEmployees) ) );

            if ( count( $includedEmployees ) == count( $tempIncludedEmployees ) ) {
                // Generate the report
                $payrollData = $this->payrollClass
                    ->partTimePayroll( $empDepartment, $earningYear, $earningMonth, $includedEmployees );

                if ( is_string( $payrollData ) ) {
                    return response()->json( [ 'ajaxFailure' => $payrollData ] );
                }

                if( count( $payrollData['data'] ) > 0 ) {
                    $ptRegularParams = [
                        'unique_id'          => $payrollData['params']['report_id'],
                        'department'         => $empDepartment,
                        'earningYear'        => $earningYear,
                        'earningMonth'       => $earningMonth,
                        'generatedBy'        => $request->session()->get( 'activeUser' )
                    ];

                    // Check if report is already generated
                    $checkIfReportExists = $this->ptReportParamsModel
                        ->where( 'department', $empDepartment )
                        ->where( 'earningYear', $earningYear )
                        ->where( 'earningMonth', $earningMonth )
                        ->get();

                    // Write values to database
                    if( isset( $checkIfReportExists[0] ) ) {
                        return response()->json( [ 'errorMessage' => 'A report was already generated for the selected department.' ] );
                    } else {
                        DB::transaction( function() use( $ptRegularParams, $payrollData ) {
                            DB::table( $this->ptReportParamsModel->table )
                                ->insert( $ptRegularParams );

                            DB::table( $this->ptReportDataModel->table )
                                ->insert( $payrollData['data'] );
                        });

                        return response()->json( [
                            'recordId' => $ptRegularParams['unique_id']
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
        } catch( \Exception $t ) {\Log::info($t->getMessage());
            // throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
            return response()->json( [ 'ajaxFailure' => $t->getMessage() ] );
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
