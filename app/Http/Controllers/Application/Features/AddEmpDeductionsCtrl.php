<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\AddEmpDeductionsRqst;

final class AddEmpDeductionsCtrl extends Controller {
    protected $deductionsModel,
              $employeeDetailsModel,
              $helperClass;

    public function __construct() {
        $this->deductionsModel      = new DeductionsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
        $this->helperClass          = new HelperClass();
    }

    public function getAction() : View {
        $employeeData = $this->employeeDetailsModel
            ->where( 'employeeNumber', '<>', '' )
            ->get();

        return view( 'Application.Features.Deductions.AddEmpDeductions' )
            ->with( 'employeeData', $employeeData );
    }

    public function postAction( AddEmpDeductionsRqst $request ) : JsonResponse {
        try {
            $deductionData   = null;
            $modeOfOperation = ( string ) $request->input( 'mode' );
            $validOperation  = null;

            // Determine what to do based on the provided operation
            if( $modeOfOperation === 'insert' ) {
                $deductionData            = $this->deductionsModel;
                $deductionData->unique_id = $this->helperClass->makeOptimizedUuid( false );
                $validOperation = true;
            } else {
                $validOperation = false;
            }

            // Checking if the operation is valid
            if( $validOperation === true ) {
                // Filter empty input
                $employeeNumber   = $this->helperClass->emptyInputFilter( $request->input( 'employeeNumber' ), true );
                $lvtPay           = $this->helperClass->emptyInputFilter( $request->input( 'lvtPay' ), true );
                $gsisPolicy       = $this->helperClass->emptyInputFilter( $request->input( 'gsisPolicy' ), true );
                $gsisEmergency    = $this->helperClass->emptyInputFilter( $request->input( 'gsisEmergency' ), true );
                $gsisUmidCa       = $this->helperClass->emptyInputFilter( $request->input( 'gsisUmidCa' ), true );
                $gsisUoliLoan     = $this->helperClass->emptyInputFilter( $request->input( 'gsisUoliLoan' ), true );
                $gsisUoliPolicy   = $this->helperClass->emptyInputFilter( $request->input( 'gsisUoliPolicy' ), true );
                $gsisEducation    = $this->helperClass->emptyInputFilter( $request->input( 'gsisEducation' ), true );
                $gsisConsolidated = $this->helperClass->emptyInputFilter( $request->input( 'gsisConsolidated' ), true );
                $gsisGfal         = $this->helperClass->emptyInputFilter( $request->input( 'gsisGfal' ), true );
                $gsisMpl          = $this->helperClass->emptyInputFilter( $request->input( 'gsisMpl' ), true );
                $gsisComputerLoan = $this->helperClass->emptyInputFilter( $request->input( 'gsisComputerLoan' ), true );
                $landBank         = $this->helperClass->emptyInputFilter( $request->input( 'landBank' ), true );
                $plmPcci          = $this->helperClass->emptyInputFilter( $request->input( 'plmPcci' ), true );
                $philamLife       = $this->helperClass->emptyInputFilter( $request->input( 'philamLife' ), true );
                $pagIbigPremium   = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigPremium' ), true );
                $pagIbigMpl       = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigMpl' ), true );
                $pagIbigEcl       = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigEcl' ), true );
                $pagIbigMp2       = $this->helperClass->emptyInputFilter( $request->input( 'pagIbigMp2' ), true );
                $nhmfc            = $this->helperClass->emptyInputFilter( $request->input( 'nhmfc' ), true );
                $maxicare         = $this->helperClass->emptyInputFilter( $request->input( 'maxicare' ), true );
                $studyGrant       = $this->helperClass->emptyInputFilter( $request->input( 'studyGrant' ), true );
                $otherBills       = $this->helperClass->emptyInputFilter( $request->input( 'otherBills' ), true );
                $atDays           = $this->helperClass->emptyInputFilter( $request->input( 'atDays' ), true );
                $atHours          = $this->helperClass->emptyInputFilter( $request->input( 'atHours' ), true );
                $atMinutes        = $this->helperClass->emptyInputFilter( $request->input( 'atMinutes' ), true );
                $manualWhTax      = $this->helperClass->emptyInputFilter( $request->input( 'manualWhTax' ), true );

                // Assign values to their respective fields;
                $deductionData->empNumber        = $employeeNumber;
                $deductionData->lvtPay           = $lvtPay;
                $deductionData->gsisConsolidated = $gsisConsolidated;
                $deductionData->gsisPolicy       = $gsisPolicy;
                $deductionData->gsisEmergency    = $gsisEmergency;
                $deductionData->gsisUmidCa       = $gsisUmidCa;
                $deductionData->gsisUoliLoan     = $gsisUoliLoan;
                $deductionData->gsisUoliPolicy   = $gsisUoliPolicy;
                $deductionData->gsisEducation    = $gsisEducation;
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

                $request->session()->flash( 'successMessage', 'Employee deduction details inserted.' );
            } else {
                return response()->json( ['ajaxFailure' => 'Invalid operation performed.'], 422 );
            }
        } catch( \PDOException $p ) {
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Database]'], 500 );
        } catch( \Throwable $t ) {
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Application]'], 500 );
        }

        // Return a response to acknowledge the receipt of the request
        return response()->json( ['ajaxSuccess' => true] );
    }
}
