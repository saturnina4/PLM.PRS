<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\JobOrder;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Classes\PayrollClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\JoReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\JoReportParametersMdl;

final class JoGenerateCtrl extends Controller {
    protected $departmentsModel,
              $employeeDetailsModel,
              $joReportDataModel,
              $joReportParamsModel,
              $helperClass,
              $payrollClass;

    public function __construct() {
        $this->departmentsModel     = new DepartmentsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
        $this->joReportDataModel    = new JoReportDataMdl();
        $this->joReportParamsModel  = new JoReportParametersMdl();
        $this->helperClass          = new HelperClass();
        $this->payrollClass         = new PayrollClass();
    }

    public function getAction() : View {
        $employeeData = $this->employeeDetailsModel
            ->where( 'employeeNumber', '<>', '' )
            ->get();

        return view( 'Application.Features.JobOrder.Reports.Generate' )
            ->with( 'employeeData', $employeeData );
    }

    public function postAction( Request $request ) : RedirectResponse {
        try {
            $jobOrderSection   = ( int ) $request->input( 'jobOrderSection' );
            $excludedEmployees = ( array ) ( $request->input( 'empExcludedList' ) ?? [] );
            $earningYear       = ( string ) substr( $request->input( 'paymentDate' ), 0, 4 );
            $earningMonth      = ( string ) substr( $request->input( 'paymentDate' ), 5, 2 );

            // Generate the report
            $payrollData = $this->payrollClass
                ->jobOrderPayroll( $jobOrderSection, $excludedEmployees, $earningYear, $earningMonth );

            if( count( $payrollData['data'] ) > 0 ) {
                // Check if report is already generated
                $checkIfReportExists = $this->joReportParamsModel
                    ->where( 'reportYear', $earningYear )
                    ->where( 'reportMonth', $earningMonth )
                    ->where( 'jobOrderSection', $jobOrderSection )
                    ->get();

                dd( $payrollData );

                // Write values to database
                if( isset( $checkIfReportExists[0] ) ) {
                    $request->session()->flash( 'errorMessage', 'A report was already generated for the selected department.' );
                } else {
                    DB::transaction( function() use( $payrollData ) : void {
                        DB::table( $this->joReportParamsModel->table )
                            ->insert( $payrollData['parameters'] );

                        DB::table( $this->joReportDataModel->table )
                            ->insert( $payrollData['data'] );
                    });

                    return redirect()->route( 'getJoViewReport', [
                        'recordId' => $payrollData['parameters']['uid']
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

        return redirect()->route( 'getJoGenReport' );
    }
}
