<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\PartTime;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Classes\ComputationsClass;
use MiSAKACHi\VERACiTY\Classes\PayrollClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\PartTimeEmpDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\OverrideLogMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\PTAddReportDataRqst;
use MiSAKACHi\VERACiTY\Http\Requests\Features\PTEditReportDataRqst;
use MiSAKACHi\VERACiTY\Traits\GeneratesUuidTrait;

final class ViewReportCtrl extends Controller {
    use GeneratesUuidTrait;

    protected $computationClass,
              $helperClass,
              $partTimeEmpDetailsModel,
              $reportDataModel,
              $reportParamsModel,
              $reportSummaryModel;

    public function __construct() {
        $this->computationClass        = new ComputationsClass();
        $this->helperClass             = new HelperClass();
        $this->payrollClass            = new PayrollClass();
        $this->partTimeEmpDetailsModel = new PartTimeEmpDetailsMdl();
        $this->reportDataModel         = new PTReportDataMdl();
        $this->reportParamsModel       = new PTReportParamsMdl();
        $this->reportSummaryModel      = new PTReportSummaryMdl();
    }

    public function getAction( Request $request, $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $reportData = $this->reportSummaryModel
                ->where( 'report_id', $recordId )
                ->orderBy( 'empName', 'asc' )
                ->orderBy( 'yearMonth', 'asc' )
                ->get();

            if( count( $reportData ) > 0 ) {
                $departmentData = $this->helperClass->getDepartmentDetails( $reportData[0]->department );
                $departmentName = $departmentData['name'];

                $earningPeriod = Carbon::createFromDate( $reportData[0]->earningYear, $reportData[0]->earningMonth, 1 );

                // // EXPERIMENTAL PROTECTION
                // $request->session()->put( 'requestId', $recordId );

                $empData = $this->partTimeEmpDetailsModel
                    ->orderBy( 'fullName', 'asc' )
                    ->get();

                return view( 'Application.Features.PartTime.ViewReportView' )
                    ->with( 'reportData', $reportData )
                    ->with( 'empData', $empData )
                    ->with( 'recordId', $recordId )
                    ->with( 'departmentName', $departmentName )
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

    public function postExclude( Request $request ) {
        try {
            $recordId = $request->input( 'recordId' );

            $reportData = $this->reportDataModel->find( $recordId );

            if ( $reportData != null ) {
                $report_id = $reportData->report_id;

                $reportData->delete();
                $request->session()->flash( 'successMessage', 'Employee was successfully excluded.' );

                $reports = $this->reportDataModel
                    ->where( 'report_id', $report_id )
                    ->get();

                if ( count( $reports ) == 0  ) {
                    $reports = $this->reportParamsModel->find( $report_id );
                    $reports->delete();

                    $request->session()->flash( 'successMessage', 'Employee was successfully excluded and the report was deleted.' );

                    return response()->json( ['ajaxSuccess' => 'empty'] );
                }
                return response()->json( ['ajaxSuccess' => 'ok'] );
            } else {
                return response()->json( ['ajaxSuccess' => 'error'] );
            }
        } catch( \PDOException $e ) {
            throw new \PDOException( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Database]'] );
        } catch( \Throwable $e ) {
            throw new \Exception( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Application]'] );
        }
    }

    public function postFetch( Request $request ) {
        try {
            $recordId = $request->input( 'recordId' );

            $reportData = $this->reportDataModel->find( $recordId );

            return response()->json([
                'noOfHrs'         => $reportData->empNoOfHrs,
                'taxPercent'      => $reportData->tax_percentage,
                'otherDeductions' => $reportData->otherDeductions,
                'yearMonth'       => $reportData->yearMonth,
                'remarks'         => $reportData->remarks
            ]);
        } catch( \PDOException $e ) {
            throw new \PDOException( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Database]'] );
        } catch( \Throwable $e ) {
            throw new \Exception( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Application]'] );
        }
    }

    public function postEdit( PTEditReportDataRqst $request ) {
        try {
            $recordId = $request->input( 'recordId' );
            $reportId = $request->input( 'reportId' );

            $reportData   = $this->reportDataModel->find( $recordId );
            $reportParams = $this->reportParamsModel->find( $reportId );

            if ( $reportData != null ) {
                $input = [
                    'empId'      => $reportData->emp_id,
                    'noOfHrs'    => $request->input( 'noOfHrs' ),
                    'remarks'    => $request->input( 'remarks' ),
                    'taxPercent' => $request->input( 'taxPercent' ),
                    'otherDeduc' => $request->input( 'otherDeductions' ),
                    'yearMonth'  => $request->input( 'yearMonth' )
                ];

                $data = $this->payrollClass->indivPartTime(
                    $input,
                    "{$reportParams->earningYear}-{$reportParams->earningMonth}",
                    $reportParams->department,
                    'edit'
                );

                if ( ! is_array( $data ) ) {
                    return response()->json( ['ajaxSuccess' => $data] );
                }

                $reportData->empNumber         = $data['empNumber'];
                $reportData->empName           = $data['empName'];
                $reportData->empDesignation    = $data['empDesignation'];
                $reportData->empAcademicType   = $data['empAcademicType'];
                $reportData->empHourlyRate     = $data['empHourlyRate'];
                $reportData->empNoOfHrs        = $data['empNoOfHrs'];
                $reportData->empEarnedAmount   = $data['empEarnedAmount'];
                $reportData->empNetAmount      = $data['empNetAmount'];
                $reportData->tax_percentage    = $data['tax_percentage'];
                $reportData->tax_ewt           = $data['tax_ewt'];
                $reportData->tax_whTax         = $data['tax_whTax'];
                $reportData->otherDeductions   = $data['otherDeductions'];
                $reportData->isTaxSysGenerated = $data['isTaxSysGenerated'];
                $reportData->yearMonth         = $data['yearMonth'];
                $reportData->remarks           = $data['remarks'];

                $reportData->save();

                $request->session()->flash( 'successMessage', 'Changes was saved.' );
                return response()->json( ['ajaxSuccess' => 'ok'] );
            } else {
                return response()->json( ['ajaxSuccess' => 'Employee not found in the report.'] );
            }
        } catch( \PDOException $e ) {
            throw new \PDOException( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Database]'] );
        } catch( \Throwable $e ) {
            throw new \Exception( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Application]'] );
        }
    }

    public function postAdd( PTAddReportDataRqst $request ) {
        try {
            $empId       = $request->input( 'empId' );
            $reportId    = $request->input( 'reportId' );

            $input = [
                'empId'       => $empId,
                'noOfHrs'     => $request->input( 'noOfHrs' ),
                'remarks'     => $request->input( 'remarks' ),
                'taxPercent' => $request->input( 'taxPercent' ),
                'otherDeduc'  => $request->input( 'otherDeductions' ),
                'yearMonth'   => $request->input( 'yearMonth' )
            ];

            $reportParams = $this->reportParamsModel->find( $reportId );
            $reportData   = new PTReportDataMdl;

            $data = $this->payrollClass->indivPartTime(
                $input,
                "{$reportParams->earningYear}-{$reportParams->earningMonth}",
                $reportParams->department,
                'add'
            );

            if ( ! is_array( $data ) ) {
                return response()->json( ['ajaxSuccess' => $data] );
            }

            $unique_id = $this->makeOptimizedUuid();

            $reportData->unique_id         = $unique_id;
            $reportData->report_id         = $reportId;
            $reportData->emp_id            = $empId;
            $reportData->empNumber         = $data['empNumber'];
            $reportData->empName           = $data['empName'];
            $reportData->empDesignation    = $data['empDesignation'];
            $reportData->empAcademicType   = $data['empAcademicType'];
            $reportData->empHourlyRate     = $data['empHourlyRate'];
            $reportData->empNoOfHrs        = $data['empNoOfHrs'];
            $reportData->empEarnedAmount   = $data['empEarnedAmount'];
            $reportData->empNetAmount      = $data['empNetAmount'];
            $reportData->tax_percentage  = $data['tax_percentage'];
            $reportData->tax_ewt           = $data['tax_ewt'];
            $reportData->tax_whTax         = $data['tax_whTax'];
            $reportData->otherDeductions   = $data['otherDeductions'];
            $reportData->isTaxSysGenerated = $data['isTaxSysGenerated'];
            $reportData->yearMonth         = $data['yearMonth'];
            $reportData->remarks           = $data['remarks'];

            $reportData->save();

            if ( $this->reportDataModel->find( $unique_id ) ) {
                $request->session()->flash( 'successMessage', 'Successfully saved.' );
                return response()->json( ['ajaxSuccess' => 'ok'] );
            } else {
                return response()->json( ['ajaxSuccess' => 'Error in saving.'] );
            }
        } catch( \PDOException $e ) {
            throw new \PDOException( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Database]'] );
        } catch( \Throwable $e ) {
            throw new \Exception( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
            return response()->json( ['ajaxFailure' => 'Critical application error occurred. [Application]'] );
        }
    }
}
