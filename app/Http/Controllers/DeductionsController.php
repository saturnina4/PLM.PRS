<?php

namespace MiSAKACHi\VERACiTY\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsModel;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsModel;
use MiSAKACHi\VERACiTY\Http\Requests\FindDeductionsRequest;
use MiSAKACHi\VERACiTY\Http\Requests\UploadDeductionsRequest;
use MiSAKACHi\VERACiTY\UDF\CommonFunctions;
use Ramsey\Uuid\Uuid;

const VALID_MIME_TYPES = [
    'text/plain',
    'text/csv'
];

class DeductionsController extends Controller {

    protected $deductionsModel;
    protected $departmentsModel;

    public function __construct() {
        $this->deductionsModel  = new DeductionsModel();
        $this->departmentsModel = new DepartmentsModel();
    }

    public function getUploadDeductions() {
        return view( 'application.Deductions.UploadDeductions' );
    }

    public function postUploadDeductions( UploadDeductionsRequest $request ) {
        if( $request->file( 'inputFile' )->isValid() ) {
            $fileObject = $request->file( 'inputFile' );
            $fileName   = $fileObject->path();
            $fileType   = $fileObject->getMimeType();
            $batchId    = CommonFunctions::optimizeUuid( Uuid::uuid1() );

            if( in_array( $fileType, VALID_MIME_TYPES ) ) {
                if( ( $fileHandle = fopen( $fileName, 'r' ) ) !== false ) {
                    try {
                        $index          = 0;
                        $deductionData  = [];

                        // Skip first line by moving file pointer
                        fgetcsv( $fileHandle );

                        // Read rows
                        while( ( $rowData = fgetcsv( $fileHandle ) ) !== false ) {
                            $deductionData[$index] = [
                                'unique_id'          => CommonFunctions::optimizeUuid( Uuid::uuid1() ),
                                'deduction_batch_id' => $batchId,
                                'empNumber'          => $rowData[1],
                                'deductionYear'      => $rowData[2],
                                'deductionMonth'     => $rowData[3],
                                'deductionPeriod'    => $rowData[4],
                                'empAbsences'        => $rowData[5],
                                'gsisPolicy'         => $rowData[6],
                                'gsisConsolidated'   => $rowData[7],
                                'gsisEmergency'      => $rowData[8],
                                'gsisUmidCa'         => $rowData[9],
                                'gsisUdliPolicy'     => $rowData[10],
                                'gsisUdliLoan'       => $rowData[11],
                                'gsisEducation'      => $rowData[12],
                                'pagIbigMpl'         => $rowData[13],
                                'pagIbigEcl'         => $rowData[14],
                                'landBank'           => $rowData[15],
                                'plmPcci'            => $rowData[16],
                                'philamLife'         => $rowData[17],
                                'studyGrant'         => $rowData[18],
                                'nhfmc'              => $rowData[19],
                                'otherBills'         => $rowData[20],
                                'manualWhTax'        => $rowData[21],
                                'manualGsisLr'       => $rowData[22],
                                'manualPhealth'      => $rowData[23]
                            ];

                            $index++;
                        }

                        if( count( $deductionData ) > 0 ) {
                            DB::transaction( function() use( $deductionData ) {
                                DB::table( $this->deductionsModel->table )
                                    ->insert( $deductionData );
                            });

                            $request->session()->flash( 'successMessage', 'Records uploaded successfully.' );
                        } else {
                            $request->session()->flash( 'errorMessage', 'Empty file uploaded.' );
                        }
                    } catch( \ErrorException $e ) {
                        $request->session()->flash( 'errorMessage', 'Invalid column count on uploaded file.' );
                    } catch( QueryException $e ) {
                        $request->session()->flash( 'errorMessage', 'Records insertion failed. Error code returned: ' . $e->getCode() );
                    } finally {
                        fclose( $fileHandle );
                    }
                } else {
                    $request->session()->flash( 'errorMessage', 'Error in reading uploaded file.' );
                }
            } else {
                $request->session()->flash( 'errorMessage', 'Invalid file uploaded.' );
            }

            return redirect( '/deductions/upload' );
        }
    }

    public function getFindDeductions() {
        return view( 'application.Deductions.FindDeductions' );
    }

    public function postFindDeductions( FindDeductionsRequest $request ) {
        $deductionData = $this->deductionsModel
            ->where( 'deductionYear', substr( $request->input( 'yearAndMonth' ), 0, 4 ) )
            ->where( 'deductionMonth', substr( $request->input( 'yearAndMonth' ), 5, 2 ) )
            ->where( 'deductionPeriod', $request->input( 'earningPeriod' ) )
            ->get();

        if( isset( $deductionData[0]->deduction_batch_id ) ) {
            return redirect( 'deductions/view/id/' . bin2hex( $deductionData[0]->deduction_batch_id ) );
        } else {
            $request->session()->flash( 'errorMessage', 'No record found for the specified parameters.' );
            return redirect( 'deductions/view/' );
        }
    }

    public function getViewDeductions( Request $request, $batchId ) {
        if( ctype_xdigit( $batchId ) && mb_strlen( $batchId ) == 32 ) {
            $deductionData = $this->deductionsModel
                ->where( 'deduction_batch_id', hex2bin( $batchId ) )
                ->get();

            if( count( $deductionData ) > 0 ) {
                $formattedDate = date_format( date_create( $deductionData[0]->deductionYear . $deductionData[0]->deductionMonth ), 'F Y' );

                return view( 'application.Deductions.ViewDeductions' )
                    ->with( 'deductionData', $deductionData )
                    ->with( 'yearAndMonth', $formattedDate )
                    ->with( 'period', 1 );
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified Batch ID.' );
            }
        } else {
            $request->session()->flash( 'errorMessage', 'Invalid Batch ID provided' );
        }

        return view( 'application.Deductions.ViewDeductions' );
    }
}
