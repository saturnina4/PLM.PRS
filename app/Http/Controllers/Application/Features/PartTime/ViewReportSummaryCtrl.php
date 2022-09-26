<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\PartTime;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Classes\ComputationsClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PartTimeEmpDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportParamsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\OverrideLogMdl;
use MiSAKACHi\VERACiTY\Http\Requests\Features\PTViewSummaryRqst;
use MiSAKACHi\VERACiTY\Traits\GeneratesUuidTrait;

final class ViewReportSummaryCtrl extends Controller {
    use GeneratesUuidTrait;

    protected $departmentsModel,
              $helperClass,
              $partTimeEmpDetailsModel,
              $reportDataModel,
              $reportParamsModel,
              $reportSummaryModel;

    public function __construct() {
        $this->departmentsModel     = new DepartmentsMdl();
        $this->computationClass        = new ComputationsClass();
        $this->helperClass             = new HelperClass();
        $this->partTimeEmpDetailsModel = new PartTimeEmpDetailsMdl();
        $this->reportDataModel         = new PTReportDataMdl();
        $this->reportParamsModel       = new PTReportParamsMdl();
        $this->reportSummaryModel      = new PTReportSummaryMdl();
    }

    public function getAction( Request $request ) {
        return view( 'Application.Features.PartTime.ViewReportSummary' );
    }

    public function postAction( PTViewSummaryRqst $request ) {
        try {
            $payPeriod  = $request->input( 'yearAndMonth' );

            $reportTotals = $this->reportSummaryModel
                ->selectRaw(
                    'SUM( empEarnedAmount ) AS empEarnedAmount, ' .
                    'SUM( tax_ewt ) AS tax_ewt, ' .
                    'SUM( tax_whTax ) AS tax_whTax, ' .
                    'SUM( otherDeductions ) AS otherDeductions, ' .
                    'SUM( empNetAmount ) AS empNetAmount'
                )
                ->where( 'earningYear', substr( $payPeriod, 0, 4 ) )
                ->where( 'earningMonth', substr( $payPeriod, 5, 2 ) )
                ->get();

            if ( count( $reportTotals ) == 1 && $reportTotals[0]->empEarnedAmount != null ) {
                $reportTotals = $reportTotals[0];

                return view( 'Application.Features.PartTime.ViewReportSummary' )
                    ->with( 'payPeriod', $payPeriod )
                    ->with( 'reportTotals', $reportTotals );
            } else {
                $request->session()->flash( 'errorMessage', 'No data found for the selected period.' );

                return redirect()->back();
            }
        } catch( \PDOException $e ) {
            $request->session()->flash( 'errorMessage', 'Critical application error occurred. [Database]' );
            throw new \PDOException( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
        } catch( \Throwable $e ) {
            $request->session()->flash( 'errorMessage', 'Critical application error occurred. [Application]' );
            throw new \Exception( basename( $e->getFile() ) . " [{$e->getLine()}] -> {$e->getMessage()}" );
        }
    }
}
