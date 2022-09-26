<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\GPReportParamsMdl;

final class GPFindReportCtrl extends Controller {
    protected $departmentsModel,
              $gpReportParamsModel;

    public function __construct() {
        $this->departmentsModel    = new DepartmentsMdl();
        $this->gpReportParamsModel = new GPReportParamsMdl();
    }

    public function getAction() : View {
        $adminDeptsList = $this->departmentsModel
            ->where( 'deptType', '1' )
            ->get();

        $acadDeptsList  = $this->departmentsModel
            ->where( 'deptType', '2' )
            ->get();

        return view( 'Application.Features.GeneralPayroll.FindReportView' )
            ->with( 'acadDeptsList', $acadDeptsList )
            ->with( 'adminDeptsList', $adminDeptsList );
    }

    public function postAction( Request $request ) : RedirectResponse {
        try {
            $reportData = $this->gpReportParamsModel
                ->where( 'department', $request->input( 'selectedDepartment' ) )
                ->where( 'earningYear', substr( $request->input( 'yearAndMonth' ), 0, 4 ) )
                ->where( 'earningMonth', substr( $request->input( 'yearAndMonth' ), 5, 2 ) )
                ->where( 'earningPeriod', $request->input( 'earningPeriod' ) )
                ->get();

            if( count( $reportData ) > 0 ) {
                info(redirect()->route( 'getGPViewReport', [
                    'recordId' => $reportData[0]->report_id
                ]));
                return redirect()->route( 'getGPViewReport', [
                    'recordId' => $reportData[0]->report_id
                ]);
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified parameters.' );
            }
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return redirect()->route( 'getGPFindReport' );
    }
}
