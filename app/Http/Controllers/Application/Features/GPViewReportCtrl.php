<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\GPReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Models\GPReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\GPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\OverrideLogMdl;

final class GPViewReportCtrl extends Controller {
    protected $helperClass,
              $reportParamsModel,
              $reportSummaryModel;

    public function __construct() {
        $this->helperClass        = new HelperClass();
        $this->reportDataModel    = new GPReportDataMdl();
        $this->reportParamsModel  = new GPReportParamsMdl();
        $this->reportSummaryModel = new GPReportSummaryMdl();
    }

    public function getAction( Request $request, $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $reportData = $this->reportSummaryModel
                ->where( 'report_id', hex2bin( $recordId ) )
                ->get();

            if( count( $reportData ) > 0 ) {
                $departmentData = $this->helperClass->getDepartmentDetails( $reportData[0]->department );
                $departmentName = $departmentData['name'];
                $payPeriodRange = $reportData[0]->payPeriodFrom . ' to ' . $reportData[0]->payPeriodTo;
                $earningPeriod  = $reportData[0]->earningPeriod;

                // EXPERIMENTAL PROTECTION
                $request->session()->put( 'requestId', $recordId );

                return view( 'Application.Features.GeneralPayroll.ViewReportView' )
                    ->with( 'reportData', $reportData )
                    ->with( 'recordId', $recordId )
                    ->with( 'departmentName', $departmentName )
                    ->with( 'payPeriodRange', $payPeriodRange )
                    ->with( 'earningPeriod', $earningPeriod );
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified record ID.' );
            }
        } else {
            $request->session()->flash( 'errorMessage', 'Invalid record ID provided' );
        }

        return redirect()->route( 'getGPFindReport' );
    }

    public function postAction( $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $reportData = $this->reportParamsModel
                ->where( 'report_id', hex2bin( $recordId ) )
                ->first();

            if( count( $reportData ) > 0 ) {
                $currentDate = date_format( date_create(), 'Y-m' );
                $currentYear = ( string ) substr( $currentDate, 0, 4 );
                $currentMonth = ( string ) substr( $currentDate, 5, 2 );

                // if( $currentYear == $reportData->earningYear &&
                    // $currentMonth == $reportData->earningMonth ) {

                    $this->reportParamsModel
                        ->where( 'report_id', hex2bin( $recordId ) )
                        ->delete();

                    session()->flash( 'successMessage', 'Report successfully deleted.' );
                // } else {
                    // session()->flash( 'errorMessage', 'Report cannot be deleted.' );
                // }

                return response()->json( ['ajaxSuccess' => true] );
            } else {
                return response()->json( ['ajaxFailure' => 'No record found for the specified record ID.'] );
            }
        }

        return response()->json( ['ajaxFailure' => 'Invalid record ID provided.'] );
    }

    public function postEditWhTax( Request $request ) {
        // Check the expected response of the client
        $clientExpectsJson = $request->expectsJson();

        try {
            $recordId = $request->input( 'recordId' );
            $newWhTax = str_replace( ',', '', $request->input( 'whTax' ));

            $reportData    = $this->reportDataModel->findByUuid( $recordId );
            $prevWhTax     = $reportData->tax_whTax;
            $prevNetSalary = $reportData->empNetSalary;
            $newNetSalary  = 0;
            $hasChanges    = -1;
            $message       = 'An error has occurred';

            if ( is_numeric( $newWhTax ) && ( $newWhTax > -1 && $newWhTax < 1073741825 ) ) {
                $newWhTax = round( $newWhTax, 2 );
                if ( $newWhTax != $prevWhTax ) {
                    $newNetSalary = $prevNetSalary + $prevWhTax - $newWhTax;
                    if ( $newNetSalary > 0 ) {
                        DB::beginTransaction();

                        $reportData->tax_whTax = $newWhTax;
                        $reportData->isTaxSysGenerated = 0;
                        $reportData->empNetSalary = $newNetSalary;
                        $saved1 = $reportData->save();

                        $prevValue = [
                            'whTax' => ( string ) $prevWhTax,
                            'netSalary' => ( string ) $prevNetSalary
                        ];

                        $newValue = [
                            'whTax' => ( string ) $newWhTax,
                            'netSalary' => ( string ) $newNetSalary
                        ];

                        $overrideLog = new OverrideLogMdl;
                        $overrideLog->record_id = $recordId;
                        $overrideLog->empNumber = session( 'activeUserId' );
                        $overrideLog->prevValue = json_encode( $prevValue );
                        $overrideLog->newValue  = json_encode( $newValue );
                        $saved2 = $overrideLog->save();

                        if ( $saved1 && $saved2 ) {
                            $hasChanges = 1;
                            $message    = 'Successfully changed.';
                            DB::commit();
                        } else {
                            DB::rollBack();
                        }
                    } else {
                        $hasChanges = -1;
                        $message    = 'Produced negative net salary.';
                    }
                } else {
                    $hasChanges = 0;
                    $message    = 'There are no changes.';
                }
            } else {
                $hasChanges = -1;
                $message    = 'Invalid value.';
            }

            return response()->json([
                'hasChanges' => $hasChanges,
                'message'    => $message,
                'whTax'      => number_format( ( float ) $newWhTax, 2, '.', ',' ),
                'netSalary'  => number_format( ( float ) $newNetSalary, 2, '.', ',' )
            ]);
        } catch( \PDOException $e ) {
            $message = basename( $e->getFile() ) . " [{$e->getLine()}] - PDO Exception.";
            return response()->json( ['error' => 'An error has occurred.'] );
        } catch( \Throwable $e ) {
            $message = basename( $e->getFile() ) . " [{$e->getLine()}] -{$e->getMessage()} Throwable Exception.";
            return response()->json( ['error' => 'An error has occurred.'] );
        }
    }
}
