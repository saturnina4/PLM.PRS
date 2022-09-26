<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\Casual;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\CPReportParamsMdl;

final class FindReportCtrl extends Controller {
    protected $ptReportParamsModel;

    public function __construct() {
        $this->cpReportParamsModel = new CPReportParamsMdl();
    }

    public function getAction() : View {
        return view( 'Application.Features.Casual.FindReportView' );
    }

    public function postAction( Request $request ) : RedirectResponse {
        try {
            $reportData = $this->cpReportParamsModel
                ->where( 'cutOffPeriod', $request->input( 'cutOffPeriod' ) )
                ->where( 'earningYear', substr( $request->input( 'yearAndMonth' ), 0, 4 ) )
                ->where( 'earningMonth', substr( $request->input( 'yearAndMonth' ), 5, 2 ) )
                ->get();

            if( count( $reportData ) > 0 ) {
                return redirect()->route( 'getCPViewReport', [
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
