<?php

namespace MiSAKACHi\VERACiTY\Http\Controllers;

use Illuminate\Http\Request;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsModel;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsModel;
use MiSAKACHi\VERACiTY\Http\Models\GPReportDataModel;
use MiSAKACHi\VERACiTY\Http\Models\GPReportParamsModel;
use MiSAKACHi\VERACiTY\Http\Models\GPRegReportSummaryModel;
use MiSAKACHi\VERACiTY\Http\Models\GsisMultiplierModel;
use MiSAKACHi\VERACiTY\Http\Models\PhilHealthContributionModel;
use MiSAKACHi\VERACiTY\Http\Models\SalaryTrancheScheduleModel;
use MiSAKACHi\VERACiTY\Http\Models\SignatoriesEffectivityModel;
use MiSAKACHi\VERACiTY\Http\Models\WithHoldingTaxModel;
use MiSAKACHi\VERACiTY\Http\Requests\GPRegReportFinderRequest;
use MiSAKACHi\VERACiTY\UDF\CommonFunctions;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

class GPController extends Controller {
                                                                                                   /* <!-- FIELDS --> */
    protected $acadDeptsList;
    protected $adminDeptsList;
    protected $departmentsModel;
    protected $deductionsModel;
    protected $employeeDetailsModel;
    protected $gpRegReportDataModel;
    protected $gpRegReportDetailsModel;
    protected $gpRegReportSummaryModel;
    protected $gsisMultiplierModel;
    protected $philHealthContributionModel;
    protected $salaryTrancheScheduleModel;
    protected $signatoriesEffectivityModel;
    protected $withholdingTaxModel;

                                                                                              /* <!-- CONSTRUCTOR --> */
    public function __construct() {
        $this->deductionsModel             = new DeductionsModel();
        $this->departmentsModel            = new DepartmentsMdl();
        $this->employeeDetailsModel        = new EmployeeDetailsModel();
        $this->gpRegReportDataModel        = new GPReportDataModel();
        $this->gpRegReportDetailsModel     = new GPReportParamsModel();
        $this->gpRegReportSummaryModel     = new GPRegReportSummaryModel();
        $this->gsisMultiplierModel         = new GsisMultiplierModel();
        $this->philHealthContributionModel = new PhilHealthContributionModel();
        $this->salaryTrancheScheduleModel  = new SalaryTrancheScheduleModel();
        $this->signatoriesEffectivityModel = new SignatoriesEffectivityModel();
        $this->withholdingTaxModel         = new WithHoldingTaxModel();

        $this->adminDeptsList = $this->departmentsModel
            ->where( 'deptType', '1' )
            ->get();

        $this->acadDeptsList  = $this->departmentsModel
            ->where( 'deptType', '2' )
            ->get();
    }
                                                                                     /* <!-- METHODS / PROPERTIES --> */
    public function getGPReportFinder() {
        return view( 'Application.GeneralPayroll.FindAndView.GPFinder' )
            ->with( 'acadDeptsList', $this->acadDeptsList )
            ->with( 'adminDeptsList', $this->adminDeptsList );
    }

    public function postGPReportFinder( GPRegReportFinderRequest $request ) {
        $reportData = $this->gpRegReportDetailsModel
            ->where( 'department', $request->input( 'selectedDepartment' ) )
            ->where( 'earningYear', substr( $request->input( 'yearAndMonth' ), 0, 4 ) )
            ->where( 'earningMonth', substr( $request->input( 'yearAndMonth' ), 5, 2 ) )
            ->where( 'earningPeriod', $request->input( 'earningPeriod' ) )
            ->get();

        if( isset( $reportData[0]->report_id ) ) {
            return redirect( 'gp/report/view/id/' . bin2hex( $reportData[0]->report_id ) );
        } else {
            $request->session()->flash( 'errorMessage', 'No record found for the specified parameters.' );
            return redirect( 'gp/report/view/' );
        }
    }

    public function getGPReportPrinter( Request $request, String $recordId ) {
        $fileExt        = env( 'REPORTS_FILE_EXTENSION', 'xlsx' );
        $fileFormat     = env( 'REPORTS_FILE_FORMAT', 'Excel2007' );
        $fileName		= uniqid() . '.' . $fileExt;
        $recordsPerPage = env( 'REPORTS_RECORDS_PER_PAGE' );
        $savePath       = env( 'REPORTS_SAVE_PATH' );
        $templateFile   = env( 'REPORTS_TEMPLATE_FILE' );
        $fullFilePath   = $savePath . $fileName;

        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $reportData = $this->gpRegReportSummaryModel
                ->where( 'report_id', hex2bin( $recordId ) )
                ->orderBy( 'empName', 'asc' )
                ->get();

            $recordCount = count( $reportData );

            if( $recordCount > 0 ) {
                $formattedPayPeriodFrom = date_format( date_create( $reportData[0]['payPeriodFrom'] ), 'F d, Y' );
                $formattedPayPeriodTo   = date_format( date_create( $reportData[0]['payPeriodTo'] ), 'F d, Y' );
                $excelReader            = PHPExcel_IOFactory::createReader( $fileFormat );
                $excelObject            = $excelReader->load( $templateFile );
                $pageCount              = ceil( $recordCount / $recordsPerPage );

                // GET THE BASE TEMPLATE
                $baseTemplate           = $excelObject->getActiveSheet();

                // CREATE PAGES FOR THE WORKBOOK
                if( $pageCount > 1 ) {
                    for( $page = 1; $page < $pageCount; $page++ ) {
                        $clonedSheet  = clone $baseTemplate;
                        $excelObject->addSheet( $clonedSheet->setTitle( 'PAGE ' . ( $page + 1 ) ) );
                    }
                }

                // ADD DATA TO PAGES
                $index = 0;
                for( $page = 1; $page <= $pageCount; $page++ ) {
                    // SET PERIOD FOR EACH PAGE
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'A3', 'FOR THE PERIOD OF: ' . $formattedPayPeriodFrom . ' to ' . $formattedPayPeriodTo )
                        ->setCellValue( 'A7', CommonFunctions::getDepartmentName( $reportData[0]['empDepartment'] ) );

                    // LIMIT NUMBER OF RECORDS PER PAGE
                    for( $cellRow = 8; $cellRow <= 32; $cellRow += 2 ) {
                        if( $reportData[$index]['isExcluded'] == 0 ) {
                            $excelObject->setActiveSheetIndex( $page - 1 )
                                ->setCellValue( 'A' . $cellRow, $reportData[$index]['empName'] )
                                ->setCellValue( 'B' . $cellRow, $reportData[$index]['empDesignation'] )
                                ->setCellValue( 'C' . $cellRow, $reportData[$index]['empBaseSalary'] )
                                ->setCellValue( 'D' . $cellRow, $reportData[$index]['empLvtPay'] )
                                ->setCellValue( 'E' . $cellRow, $reportData[$index]['empPeraNet'] )
                                ->setCellValue( 'F' . $cellRow, $reportData[$index]['empAbsences'] )
                                ->setCellValue( 'G' . $cellRow, $reportData[$index]['empGrossSalary'] )
                                ->setCellValue( 'H' . $cellRow, $reportData[$index]['empGsisTotal'] )
                                ->setCellValue( 'I' . $cellRow, $reportData[$index]['empWhTax'] )
                                ->setCellValue( 'J' . $cellRow, $reportData[$index]['empPhealth'] )
                                ->setCellValue( 'K' . $cellRow, $reportData[$index]['empPagIbigTotal'] )
                                ->setCellValue( 'L' . $cellRow, $reportData[$index]['empPlmPcci'] )
                                ->setCellValue( 'M' . $cellRow, $reportData[$index]['empLandBank'] )
                                ->setCellValue( 'N' . $cellRow, $reportData[$index]['empPhilamLife'] )
                                ->setCellValue( 'O' . $cellRow, $reportData[$index]['empStudyGrant'] )
                                ->setCellValue( 'P' . $cellRow, $reportData[$index]['empOtherBillsTotal'] );
                        } else {
                            $excelObject->setActiveSheetIndex( $page - 1 )
                                ->mergeCells( "C{$cellRow}:Q{$cellRow}" );

                            $excelObject->getActiveSheet()
                                ->getStyle( "C{$cellRow}:Q{$cellRow}" )
                                ->getAlignment()
                                ->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

                            $excelObject->getActiveSheet()
                                ->setCellValue( 'A' . $cellRow, $reportData[$index]['empName'] )
                                ->setCellValue( 'B' . $cellRow, $reportData[$index]['empDesignation'] )
                                ->setCellValue( 'C' . $cellRow, "EXCLUDED PER ADVICE" );
                        }

                        // INCREMENT TO PROCESS THE NEXT RECORD
                        $index++;

                        // CHECK IF THERE ARE ANY RECORDS, IF NONE, BREAK TO REDUCE UNNECESSARY CYCLES
                        if( ! isset( $reportData[$index] ) ){
                            break;
                        }
                    }
                }

                // FINALLY, WRITE THE GENERATED REPORT
                $excelWriter	= PHPExcel_IOFactory::createWriter( $excelObject, $fileFormat );
                $excelWriter->save( $fullFilePath );

                // SEND DOWNLOAD HEADER
                header( 'Cache-Control: must-revalidate' );
                header( 'Content-Description: File Transfer' );
                header( 'Content-Disposition: attachment; filename=' . $fileName );
                header( 'Content-Length:' . filesize( $fullFilePath ) );
                header( 'Content-Type: application/octet-stream' );
                header( 'Expires: 0' );
                header( 'Pragma: public' );
                readfile( $fullFilePath );
                unlink( $fullFilePath );

                // BYE :)
                die();
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified record ID.' );
            }
        } else {
            $request->session()->flash( 'errorMessage', 'Invalid record ID provided' );
        }

        return view( 'Application.GeneralPayroll..FindAndView.GPReportViewer' );
    }

    public function getGPReportViewer( Request $request, $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) == 32 ) {
            $reportData = $this->gpRegReportSummaryModel
                ->where( 'report_id', hex2bin( $recordId ) )
                ->get();

            if( count( $reportData ) > 0 ) {
                $departmentName = CommonFunctions::getDepartmentName( $reportData[0]['empDepartment'] );
                $payPeriodRange = $reportData[0]['payPeriodFrom'] . ' to ' . $reportData[0]['payPeriodTo'];
                $earningPeriod  = $reportData[0]['earningPeriod'];

                // EXPERIMENTAL PROTECTION
                $request->session()->put( 'requestId', $recordId );

                return view( 'application.GeneralPayroll.FindAndView.GPViewer' )
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

        return view( 'Application.GeneralPayroll.FindAndView.GPViewer' );
    }
}
