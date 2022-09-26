<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\Excluded;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\EPReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EPReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\EPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Traits\GeneratesUuidTrait;

final class ViewReportCtrl extends Controller {
    use GeneratesUuidTrait;

    protected $reportDataModel,
              $reportParamsModel,
              $reportSummaryModel;

    public function __construct() {
        $this->reportDataModel    = new EPReportDataMdl();
        $this->reportParamsModel  = new EPReportParamsMdl();
        $this->reportSummaryModel = new EPReportSummaryMdl();
    }

    public function getAction( Request $request, $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $reportData = $this->reportSummaryModel
                ->where( 'report_id', $recordId )
                ->orderBy( 'departmentName', 'asc' )
                ->orderBy( 'empName', 'asc' )
                ->get();

            if( count( $reportData ) > 0 ) {
                $earningPeriod = Carbon::createFromDate( $reportData[0]->earningYear, $reportData[0]->earningMonth, 1 );

                // // EXPERIMENTAL PROTECTION
                // $request->session()->put( 'requestId', $recordId );
                if ( $reportData[0]->cutOffPeriod == 1 ) {
                    $datePeriod = '1-15';
                } else {
                    $datePeriod = '16-' . $earningPeriod->endOfMonth()->format('j');   
                }
                
                $earningPeriod = $earningPeriod->format('F') . ' ' . $datePeriod . ', ' . $earningPeriod->format('Y');

                return view( 'Application.Features.Excluded.ViewReportView' )
                    ->with( 'reportData', $reportData )
                    ->with( 'recordId', $recordId )
                    ->with( 'earningPeriod', $earningPeriod );
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified record ID.' );
            }
        } else {
            $request->session()->flash( 'errorMessage', 'Invalid record ID provided' );
        }

        return redirect()->route( 'getPTFindReport' );
    }

    public function postAction( $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $report = $this->reportParamsModel
                ->where( 'unique_id', $recordId )
                ->first();

            if( count( $report ) > 0 ) {
                // $currentDate = date_format( date_create(), 'Y-m' );
                // $currentYear = ( string ) substr( $currentDate, 0, 4 );
                // $currentMonth = ( string ) substr( $currentDate, 5, 2 );

                // if( $currentYear == $reportData->earningYear &&
                //     $currentMonth == $reportData->earningMonth ) {

                    $this->reportParamsModel
                        ->where( 'unique_id', $recordId )
                        ->delete();

                    session()->flash( 'successMessage', 'Report successfully deleted.' );
                // } else {
                //     session()->flash( 'errorMessage', 'Report cannot be deleted.' );
                // }

                return response()->json( ['ajaxSuccess' => true] );
            } else {
                return response()->json( ['ajaxFailure' => 'No record found for the specified record ID.'] );
            }
        }

        return response()->json( ['ajaxFailure' => 'Invalid record ID provided.'] );
    }
}
