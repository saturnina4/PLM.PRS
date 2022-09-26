<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\PartTime;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportParamsMdl;

final class FindReportCtrl extends Controller {
    protected $departmentsModel,
              $ptReportParamsModel;

    public function __construct() {
        $this->departmentsModel    = new DepartmentsMdl();
        $this->ptReportParamsModel = new PTReportParamsMdl();
    }

    public function getAction() : View {
        $acadDeptsList  = $this->departmentsModel
            ->where( 'deptType', '2' )
            ->get();

        return view( 'Application.Features.PartTime.FindReportView' )
            ->with( 'acadDeptsList', $acadDeptsList );
    }

    public function postAction( Request $request ) : RedirectResponse {
        try {
            $reportData = $this->ptReportParamsModel
                ->where( 'department', $request->input( 'selectedDepartment' ) )
                ->where( 'earningYear', substr( $request->input( 'yearAndMonth' ), 0, 4 ) )
                ->where( 'earningMonth', substr( $request->input( 'yearAndMonth' ), 5, 2 ) )
                ->get();

            if( count( $reportData ) > 0 ) {
                return redirect()->route( 'getPTViewReport', [
                    'recordId' => $reportData[0]->unique_id
                ]);
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified parameters.' );
            }
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return redirect()->route( 'getPTFindReport' );
    }
}
