<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\JobOrder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\JoEmployeeDataMdl;

final class JoModifyParametersCtrl extends Controller {
    public function __construct() {
        // TODO
    }

    public function getAction( $recordId ) {
        $employeeDetailsModel = new EmployeeDetailsMdl();
        $joEmployeeDataModel  = new JoEmployeeDataMdl();

        $joEmployeeData = DB::table( "{$joEmployeeDataModel->table} AS jed" )
            ->select( "jed.*", 'ed.employeeNumber', 'ed.fullName', 'ed.salaryAmount' )
            ->join( "{$employeeDetailsModel->table} AS ed", 'ed.employeeNumber', '=', 'jed.employeeNumber' )
            ->where( 'uid', $recordId )
            ->first();

        if( isset( $joEmployeeData ) ) {
            // EXPERIMENTAL PROTECTION
            session()->put( 'recordId', $recordId ); // The ID of the record that is being updated

            return view( 'Application.Features.JobOrder.Parameters.Modify' )
                ->with( 'joEmployeeData', $joEmployeeData )
                ->with( 'recordId', $recordId );
        } else {
            session()->flash( 'errorMessage', 'No record found for the given ID.' );
            return redirect()->route( 'getJoParamList' );
        }
    }

    public function postAction( Request $request, $recordId ) {
        try {
            $joEmployeeDataModel = new JoEmployeeDataMdl();

            if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) === 32 || true ) {
                $joEmployeeData  = null;
                $modeOfOperation = ( string ) $request->input( 'mode' );
                $validOperation  = null;

                // Determine what to do based on the provided operation
                if( $modeOfOperation === 'update' ) {
                    if( $recordId === $request->session()->get( 'recordId' ) ) {
                        $request->session()->forget( 'recordId' ); // EXPERIMENTAL PROTECTION
                        $joEmployeeData = $joEmployeeDataModel
                            ->find( $recordId );
                        if( isset( $joEmployeeData ) ) {
                            $validOperation = true;
                        }
                    }
                } else {
                    $validOperation = false;
                }

                // Checking if the operation is valid
                if( $validOperation === true ) {
                    // Assign values to their respective fields;
                    $joEmployeeData->daysWorked               = $request->input( 'daysWorked' );
                    $joEmployeeData->ordinaryDayHours         = $request->input( 'ordinaryDayHours' );
                    $joEmployeeData->restDayHours             = $request->input( 'restDayHours' );
                    $joEmployeeData->specialHolidayHours      = $request->input( 'specialHolidayHours' );
                    $joEmployeeData->rdAndSpecialHolidayHours = $request->input( 'rdAndSpecialHolidayHours' );
                    $joEmployeeData->regularHolidayHours      = $request->input( 'regularHolidayHours' );
                    $joEmployeeData->rhAndSpecialHolidayHours = $request->input( 'rhAndSpecialHolidayHours' );
                    $joEmployeeData->doubleHolidayHours       = $request->input( 'doubleHolidayHours' );
                    $joEmployeeData->rdAndDoubleHolidayHours  = $request->input( 'rdAndDoubleHolidayHours' );
                    $joEmployeeData->cutOffEarnings           = $request->input( 'cutOffEarnings' );
                    $joEmployeeData->otherEarnings            = $request->input( 'otherEarnings' );
                    $joEmployeeData->pagIbigPremium           = $request->input( 'pagIbigPremium' );
                    $joEmployeeData->atDays                   = $request->input( 'atDays' );
                    $joEmployeeData->atHours                  = $request->input( 'atHours' );
                    $joEmployeeData->atMinutes                = $request->input( 'atMinutes' );

                    // Process operation as a transaction
                    DB::transaction( function() use ( $joEmployeeData ) : void {
                        $joEmployeeData->save();
                    });

                    if( $modeOfOperation === 'insert' ) {
                        $request->session()->flash( 'successMessage', 'Employee parameters inserted.' );
                    } else {
                        $request->session()->flash( 'successMessage', 'Employee parameters updated.' );
                    }
                } else {
                    return response()->json( ['ajaxFailure' => 'Invalid operation performed.'], 422 );
                }
            } else {
                return response()->json( ['ajaxFailure' => 'Invalid Record ID received.'], 422 );
            }
        } catch( \PDOException $p ) {
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Database]'], 500 );
        } catch( \Throwable $t ) {
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Application]' . $t->getMessage()], 500 );
        }

        // Return a response to acknowledge the receipt of the request
        return response()->json( ['ajaxSuccess' => true] );
    }
}
