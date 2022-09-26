<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\JobOrder;

use Illuminate\Http\Request;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\JoReportParametersMdl;

final class JOViewReportCtrl extends Controller {
    protected $helperClass,
              $joParametersMdl;

    public function __construct() {
        $this->helperClass     = new HelperClass();
        $this->joParametersMdl = new JoReportParametersMdl();
    }

    public function getAction( Request $request, $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $reportData = $this->joParametersMdl
                ->find( $recordId )
                ->reportData;

            if( count( $reportData ) > 0 ) {
                // EXPERIMENTAL PROTECTION
                $request->session()->put( 'requestId', $recordId );

                return view( 'Application.Features.JobOrder.Reports.View' )
                    ->with( 'reportData', $reportData )
                    ->with( 'recordId', $recordId );
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified record ID.' );
            }
        } else {
            $request->session()->flash( 'errorMessage', 'Invalid record ID provided' );
        }

        return redirect()->route( 'getGPFindReport' );
    }
}
