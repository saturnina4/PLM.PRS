<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\ViewEmpDeductionsRqst;

final class ViewEmpDeductionsCtrl extends Controller {
    protected $deductionsModel,
              $employeeDetailsModel,
              $helperClass;

    public function __construct() {
        $this->deductionsModel      = new DeductionsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
        $this->helperClass          = new HelperClass();
    }

    // Add checking for record id
    public function getAction( Request $request, $recordId ) : View {
        $employeeDeductions = $this->deductionsModel
            ->select( "{$this->deductionsModel->table}.*", 'ed.employeeNumber', 'ed.fullName' )
            ->join( "{$this->employeeDetailsModel->table} AS ed", 'ed.employeeNumber', '=', "{$this->deductionsModel->table}.empNumber" )
            ->where( 'unique_id', hex2bin( $recordId ) )
            ->get();

        // EXPERIMENTAL PROTECTION
        $request->session()->put( 'recordId', $recordId ); // The ID of the record that is being updated

        return view( 'Application.Features.Deductions.ViewEmpDeductions' )
           ->with( 'employeeDeductions', $employeeDeductions )
           ->with( 'recordId', $recordId );
    }

    public function postAction( ViewEmpDeductionsRqst $request, $recordId ) : JsonResponse {
        try {
            if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) === 32 ) {
                $deductionData   = null;
                $modeOfOperation = ( string ) $request->input( 'mode' );
                $validOperation  = null;

                // Determine what to do based on the provided operation
                if( $modeOfOperation === 'update' ) {
                    if( $recordId === $request->session()->get( 'recordId' ) ) {
                        $request->session()->forget( 'recordId' ); // EXPERIMENTAL PROTECTION
                        $deductionData = $this->deductionsModel
                            ->findByUuid( $recordId );
                        if( isset( $deductionData ) ) {
                            $validOperation = true;
                        }
                    }
                } else {
                    $validOperation = false;
                }

                // Checking if the operation is valid
                if( $validOperation === true ) {
                    // Filter empty input
                    $lvtPay           = $this->helperClass->emptyInputFilter( $request->input( 'lvtPay' ) );
                    $gsisPolicy       = $this->helperClass->emptyInputFilter( $request->input( 'gsisPolicy' ) );
                    $gsisEmergency    = $this->helperClass->emptyInputFilter( $request->input( 'gsisEmergency' ) );
                    $gsisUmidCa       = $this->helperClass->emptyInputFilter( $request->input( 'gsisUmidCa' ) );
                    $gsisUoliLoan     = $this->helperClass->emptyInputFilter( $request->input( 'gsisUoliLoan' ) );
                    $gsisUoliPolicy   = $this->helperClass->emptyInputFilter( $request->input( 'gsisUoliPolicy' ) );
                    $gsisEducation    = $this->helperClass->emptyInputFilter( $request->input( 'gsisEducation' ) );
                    $gsisConsolidated = $this->helperClass->emptyInputFilter( $request->input( 'gsisConsolidated' ) );
                    $gsisGfal         = $this->helperClass->emptyInputFilter( $request->input( 'gsisGfal' ) );
                    $gsisMpl          = $this->helperClass->emptyInputFilter( $request->input( 'gsisMpl' ) );
                    $gsisComputerLoan = $this->helperClass->emptyInputFilter( $request->input( 'gsisComputerLoan' ) );
                    $landBank         = $this->helperClass->emptyInputFilter( $request->input( 'landBank' ) );
                    $plmPcci          = $this->helperClass->emptyInputFilter( $request->input( 'plmPcci' ) );
                    $philamLife       = $this->helperClass->emptyInputFilter( $request->input( 'philamLife' ) );
                    $pagIbigPremium   = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigPremium' ) );
                    $pagIbigMpl       = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigMpl' ) );
                    $pagIbigEcl       = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigEcl' ) );
                    $pagIbigMp2       = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigMp2' ) );
                    $nhmfc            = $this->helperClass->emptyInputFilter( $request->input( 'nhmfc' ) );
                    $maxicare         = $this->helperClass->emptyInputFilter( $request->input( 'maxicare' ) );
                    $studyGrant       = $this->helperClass->emptyInputFilter( $request->input( 'studyGrant' ) );
                    $otherBills       = $this->helperClass->emptyInputFilter( $request->input( 'otherBills' ) );
                    $atDays           = $this->helperClass->emptyInputFilter( $request->input( 'atDays' ) );
                    $atHours          = $this->helperClass->emptyInputFilter( $request->input( 'atHours' ) );
                    $atMinutes        = $this->helperClass->emptyInputFilter( $request->input( 'atMinutes' ) );
                    $manualWhTax      = $this->helperClass->emptyInputFilter( $request->input( 'manualWhTax' ) );

                    // Assign values to their respective fields;
                    $deductionData->lvtPay           = $lvtPay;
                    $deductionData->gsisPolicy       = $gsisPolicy;
                    $deductionData->gsisEmergency    = $gsisEmergency;
                    $deductionData->gsisUmidCa       = $gsisUmidCa;
                    $deductionData->gsisUoliLoan     = $gsisUoliLoan;
                    $deductionData->gsisUoliPolicy   = $gsisUoliPolicy;
                    $deductionData->gsisEducation    = $gsisEducation;
                    $deductionData->gsisConsolidated = $gsisConsolidated;
                    $deductionData->gsisGfal         = $gsisGfal;
                    $deductionData->gsisMpl          = $gsisMpl;
                    $deductionData->gsisComputerLoan = $gsisComputerLoan;
                    $deductionData->landBank         = $landBank;
                    $deductionData->plmPcci          = $plmPcci;
                    $deductionData->philamLife       = $philamLife;
                    $deductionData->pagIbigPremium   = $pagIbigPremium;
                    $deductionData->pagIbigMpl       = $pagIbigMpl;
                    $deductionData->pagIbigEcl       = $pagIbigEcl;
                    $deductionData->pagIbigMp2       = $pagIbigMp2;
                    $deductionData->nhmfc            = $nhmfc;
                    $deductionData->maxicare         = $maxicare;
                    $deductionData->studyGrant       = $studyGrant;
                    $deductionData->otherBills       = $otherBills;
                    $deductionData->atDays           = $atDays;
                    $deductionData->atHours          = $atHours;
                    $deductionData->atMinutes        = $atMinutes;
                    $deductionData->manualWhTax      = $manualWhTax;

                    // Process operation as a transaction
                    DB::transaction( function() use ( $deductionData ) {
                        $deductionData->save();
                    });

                    if( $modeOfOperation === 'insert' ) {
                        $request->session()->flash( 'successMessage', 'Employee deduction details inserted.' );
                    } else {
                        $request->session()->flash( 'successMessage', 'Employee deduction details updated.' );
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
