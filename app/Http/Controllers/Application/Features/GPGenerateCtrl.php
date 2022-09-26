<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Classes\PayrollClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\ExclusionReasonsMdl;
use MiSAKACHi\VERACiTY\Http\Models\GPReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\GPReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\GPGenerateRqst;

final class GPGenerateCtrl extends Controller {
    protected $departmentsModel,
              $employeeDetailsModel,
              $gpReportDataModel,
              $gpReportParamsModel,
              $helperClass,
              $payrollClass;

    public function __construct() {
        $this->departmentsModel     = new DepartmentsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
        $this->exclusionReasonsMdl  = new ExclusionReasonsMdl();
        $this->gpReportDataModel    = new GPReportDataMdl();
        $this->gpReportParamsModel  = new GPReportParamsMdl();
        $this->helperClass          = new HelperClass();
        $this->payrollClass         = new PayrollClass();
    }

    public function getAction() : View {
        $adminDeptsList = $this->departmentsModel
            ->where( 'deptType', '1' )
            ->get();

        $acadDeptsList = $this->departmentsModel
            ->where( 'deptType', '2' )
            ->get();

        $exclusionsList = $this->exclusionReasonsMdl
            ->where( 'id', '<>', '0' )
            ->get();

        $employeeData = $this->employeeDetailsModel
            ->where( 'employeeNumber', '<>', '' )
            ->get();

        return view( 'Application.Features.GeneralPayroll.GeneratorView' )
            ->with( 'acadDeptsList', $acadDeptsList )
            ->with( 'adminDeptsList', $adminDeptsList )
            ->with( 'exclusionsList', $exclusionsList )
            ->with( 'employeeData', $employeeData );
    }

    public function postAction( GPGenerateRqst $request ) : RedirectResponse {
        try {
            $reportType        = ( int ) $request->input( 'reportType' );
            $empDepartment     = ( int ) $request->input( 'selectedDepartment' );
            $excludedEmployees = ( array ) ( $request->input( 'empExcludedList' ) ?? [] );
            $earningYear       = ( string ) substr( $request->input( 'paymentDate' ), 0, 4 );
            $earningMonth      = ( string ) substr( $request->input( 'paymentDate' ), 5, 2 );
            $earningPeriod     = ( string ) $request->input( 'earningPeriod' );
            $payPeriodFrom     = ( string ) $request->input( 'payPeriodFrom' );
            $payPeriodTo       = ( string ) $request->input( 'payPeriodTo' );
            $whTaxOverride     = ( string ) ( $request->input( 'whTax' ) ?? 'off' );
            $overrides         = ( array ) [
                'whTax' => ( bool ) ( ( strtolower( $whTaxOverride ) )  === 'on' ? true : false )
            ];

            // Generate the report
            $payrollData = $this->payrollClass
                ->generalPayroll( $empDepartment, $reportType, $earningYear, $earningMonth, $excludedEmployees, $overrides );

            if( count( $payrollData['data'] ) > 0 ) {
                $gpRegularParams = [
                    'unique_id'          => $this->helperClass->makeOptimizedUuid( false ),
                    'report_id'          => $payrollData['params']['report_id'],
                    'reportType'         => $reportType,
                    'department'         => $empDepartment,
                    'earningYear'        => $earningYear,
                    'earningMonth'       => $earningMonth,
                    'earningPeriod'      => $earningPeriod,
                    'effectivity_id'     => $payrollData['params']['signatories'],
                    'usedTrancheVersion' => $payrollData['params']['usedTranche'],
                    'usedTranche'        => $payrollData['params']['trancheVersion'],
                    'gsis_employeeShare' => $payrollData['params']['gsis_employeeShare'],
                    'gsis_employerShare' => $payrollData['params']['gsis_employerShare'],
                    'payPeriodFrom'      => $payPeriodFrom,
                    'payPeriodTo'        => $payPeriodTo,
                    'generatedBy'        => $request->session()->get( 'activeUser' )
                ];

                // Check if report is already generated
                $checkIfReportExists = $this->gpReportParamsModel
                    ->where( 'department', $empDepartment )
                    ->where( 'earningYear', $earningYear )
                    ->where( 'earningMonth', $earningMonth )
                    ->where( 'earningPeriod', $earningPeriod )
                    ->where( 'reportType', $reportType )
                    ->get();

                // Write values to database
                if( isset( $checkIfReportExists[0] ) ) {
                    $request->session()->flash( 'errorMessage', 'A report was already generated for the selected department.' );
                } else {
                    DB::transaction( function() use( $gpRegularParams, $payrollData ) {
                        DB::table( $this->gpReportParamsModel->table )
                            ->insert( $gpRegularParams );

                        DB::table( $this->gpReportDataModel->table )
                            ->insert( $payrollData['data'] );
                    });

                    return redirect()->route( 'getGPViewReport', [
                        'recordId' => bin2hex( $gpRegularParams['report_id'] )
                    ]);
                }
            } else {
                $request->session()->flash( 'errorMessage', 'No employees found in the specified department.' );
            }
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return redirect()->route( 'getGPGenReport' );
    }
}
