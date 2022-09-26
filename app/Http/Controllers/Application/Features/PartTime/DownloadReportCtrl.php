<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\PartTime;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTSignatoriesMdl;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

final class DownloadReportCtrl extends Controller {
    protected $empDetailsModel,
              $ptReportSummaryModel,
              $signatoriesModel,
              $helperClass;

    public function __construct() {
        $this->empDetailsModel      = new EmployeeDetailsMdl();
        $this->ptReportSummaryModel = new PTReportSummaryMdl();
        $this->ptReportSummaryModel = new PTReportSummaryMdl();
        $this->signatoriesModel     = new PTSignatoriesMdl();
        $this->helperClass          = new HelperClass();
    }

    public function getAction( Request $request, string $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) === 32 ) {
            $fileExt        = env( 'PT_REPORTS_FILE_EXTENSION', 'xlsm' );
            $fileFormat     = env( 'REPORTS_FILE_FORMAT', 'Excel2007' );
            $recordsPerPage = env( 'PT_REPORTS_RECORDS_PER_PAGE' );
            $savePath       = env( 'REPORTS_SAVE_PATH' );
            $templateFile   = env( 'PT_REPORTS_TEMPLATE_FILE' );

            // Get the report data
            $reportData = $this->ptReportSummaryModel
                ->where( 'report_id', $recordId )
                ->orderBy( 'empName', 'asc' )
                ->orderBy( 'yearMonth', 'asc' )
                ->get();

            // Get the signatories
            $signatories = $this->signatoriesModel
                ->join( "{$this->empDetailsModel->table} as ecd", 'empNumber', '=', 'ecd.employeeNumber' )
                ->select( 'formalName', 'position' )
                ->orderBy( 'id', 'asc' )
                ->get();

            // Count the number of results
            $recordCount = count( $reportData );

            // Start the result processing
            if( $recordCount > 0 ) {
                // Get the department details
                $departmentData = $this->helperClass
                    ->getDepartmentDetails( $reportData[0]->department );
                $departmentName      = $departmentData['name'];
                $departmentCode      = $departmentData['code'];
                $departmentHead      = $departmentData['deptHead'];
                $departmentHeadTitle = $departmentData['deptHeadTitle'];

                $earningYear  = $reportData[0]->earningYear;
                $earningMonth = $reportData[0]->earningMonth;

                // Set the report parameters
                $payPeriod = \Carbon\Carbon::createFromDate( $earningYear, $earningMonth, 1 )
                    ->format('F, Y');

                $generatedOn   = $reportData[0]->date_created;
                $excelReader   = PHPExcel_IOFactory::createReader( $fileFormat );
                $excelObject   = $excelReader->load( $templateFile );
                $pageCount     = ceil( $recordCount / $recordsPerPage );
                $fileName      = "{$departmentCode}-[{$earningYear}-{$earningMonth}].{$fileExt}";
                $filePath      = $savePath . $fileName;
                $pageName      = 'PAGE';

                // Get the base template
                $baseTemplate  = $excelObject->getActiveSheet()->setTitle( "{$pageName} 1" );

                // Create pages for the workbook
                if( $pageCount > 1 ) {
                    // Start naming at page 2 since page 1 is already on the worksheet
                    for( $page = 1; $page < $pageCount; $page++ ) {
                        $clonedSheet = clone $baseTemplate;
                        $pageNumber  = $page + 1;
                        $excelObject->addSheet( $clonedSheet->setTitle( "{$pageName} {$pageNumber}" ) );
                    }
                }

                // Now, build the report by adding the data
                $index = 0;
                for( $page = 1; $page <= $pageCount; $page++ ) {
                    // Set various page parameters
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        // Set department name
                        ->setCellValue( 'A6', $departmentName )
                        // Set payroll period
                        ->setCellValue( 'A7', 'Pay Period  : ' . strtoupper( $payPeriod ) )
                        // Add the date wherein the report was generated
                        ->setCellValue( 'A52', "GENERATED ON {$generatedOn}" )
                        // Set current page / total pages
                        ->setCellValue( 'M52', "[ {$page} / {$pageCount} ]" );

                    // Limit number of rows per page
                    for( $cellRow = 9; $cellRow <= 37; $cellRow += 2 ) {
                        if ( $reportData[$index]['yearMonth'] == "{$earningYear}-{$earningMonth}" ) {
                            $remarks = ( $reportData[$index]['empAcademicType'] == 'G' ? 'GP' : '' ) . ( $reportData[$index]['remarks'] != '' ? ' - ' . $reportData[$index]['remarks'] : '' );
                        } else {
                            $remarks = strtoupper( \Carbon\Carbon::createFromDate(
                                    substr( $reportData[$index]['yearMonth'], 0, 4 ),
                                    substr( $reportData[$index]['yearMonth'], 5 ), 1
                                )
                                ->format("M'y") ) . ( $reportData[$index]['empAcademicType'] == 'G' ? ' - GP' : '' ) .
                                ( $reportData[$index]['remarks'] != '' ? ' - ' . $reportData[$index]['remarks'] : '' );
                        }

                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( 'A' . $cellRow, ( $index + 1 ) )
                            ->setCellValue( 'B' . $cellRow, $reportData[$index]['empName'] )
                            ->setCellValue( 'C' . $cellRow, $reportData[$index]['empDesignation'] )
                            ->setCellValue( 'D' . $cellRow, $reportData[$index]['empHourlyRate'] )
                            ->setCellValue( 'E' . $cellRow, $reportData[$index]['empNoOfHrs'] )
                            ->setCellValue( 'F' . $cellRow, $reportData[$index]['empEarnedAmount'] )
                            ->setCellValue( 'G' . $cellRow, "=IF({$reportData[$index]['tax_ewt']}=0,\"\",{$reportData[$index]['tax_ewt']})" )
                            ->setCellValue( 'H' . $cellRow, "=IF({$reportData[$index]['tax_whTax']}=0,\"\",{$reportData[$index]['tax_whTax']})" )
                            ->setCellValue( 'I' . $cellRow, "=IF({$reportData[$index]['otherDeductions']}=0,\"\",{$reportData[$index]['otherDeductions']})" )
                            ->setCellValue( 'J' . $cellRow, $reportData[$index]['empNetAmount'] )
                            ->setCellValue( 'K' . $cellRow, ( $index + 1 ) )
                            ->setCellValue( 'M' . $cellRow, $remarks );

                        $excelObject->getActiveSheet()->getStyle( 'C' . $cellRow )
                            ->getAlignment()->setWrapText( true );

                        // Increment to process the next row
                        $index++;

                        // Stop if there are no more rows to reduce unnecessary cycles
                        if( ! isset( $reportData[$index] ) ){
                            // Finally, loop laid unto eternal rest :)
                            break;
                        }
                    }

                    if ( $page != $pageCount ) {
                        // Add the total of this page in the "Sub-Total" row
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( 'A39', 'Sub-Total   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -' )
                            ->setCellValue( 'F39', "=IF(SUM('PAGE 1:PAGE {$page}'!F9:F38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!F9:F38))" )
                            ->setCellValue( 'G39', "=IF(SUM('PAGE 1:PAGE {$page}'!G9:G38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!G9:G38))" )
                            ->setCellValue( 'H39', "=IF(SUM('PAGE 1:PAGE {$page}'!H9:H38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!H9:H38))" )
                            ->setCellValue( 'I39', "=IF(SUM('PAGE 1:PAGE {$page}'!I9:I38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!I9:I38))" )
                            ->setCellValue( 'J39', "=IF(SUM('PAGE 1:PAGE {$page}'!J9:J38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!J9:J38))" );
                    } else {
                        // Add the overall total in the "Grand Total" row
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( 'A39', 'Grand Total   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -' )
                            ->setCellValue( 'F39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!F9:F38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!F9:F38))" )
                            ->setCellValue( 'G39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!G9:G38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!G9:G38))" )
                            ->setCellValue( 'H39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!H9:H38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!H9:H38))" )
                            ->setCellValue( 'I39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!I9:I38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!I9:I38))" )
                            ->setCellValue( 'J39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!J9:J38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!J9:J38))" );

                        $excelObject->getActiveSheet()->getStyle( 'A39' )->getFont()->setBold( true );

                        for ( $i = 'F'; $i <= 'J' ; ++$i ) {
                            $excelObject->getActiveSheet()->getStyle( "{$i}39" )->getFont()->setBold( true );
                            $excelObject->getActiveSheet()->getStyle( "{$i}39" )->getBorders()->getTop()->setBorderStyle( \PHPExcel_Style_Border::BORDER_THICK );
                        }
                    }

                    // Add the certified amount to the signatory
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'C45', "=SUM('PAGE 1:PAGE {$pageCount}'!J9:J38)" );

                    // Add the Department Signatory
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'I43', $departmentHead )
                        ->setCellValue( 'I44', $departmentHeadTitle );

                    // Add Other Signatories
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'A43', $signatories[0]->formalName )
                        ->setCellValue( 'A44', $signatories[0]->position );

                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'D43', $signatories[1]->formalName )
                        ->setCellValue( 'D44', $signatories[1]->position );

                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'A49', $signatories[2]->formalName )
                        ->setCellValue( 'A50', $signatories[2]->position );

                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'G49', $signatories[3]->formalName )
                        ->setCellValue( 'G50', $signatories[3]->position );

                    // Protect the sheet.
                    $sheetPassword = env( 'SHEET_PASSWORD', 'u2Ns718*HI>90(ZT' );
                    $excelObject->getActiveSheet()->getProtection()->setSheet( true );
                    $excelObject->getActiveSheet()->getProtection()->setSort( true );
                    $excelObject->getActiveSheet()->getProtection()->setInsertRows( false );
                    $excelObject->getActiveSheet()->getProtection()->setFormatCells( false );
                    $excelObject->getActiveSheet()->getProtection()->setPassword( $sheetPassword );
                }

                // Finally, save the generated report
                $excelWriter = PHPExcel_IOFactory::createWriter( $excelObject, $fileFormat );
                $excelWriter->save( $filePath );

                // Send download header
                header( 'Cache-Control: must-revalidate' );
                header( 'Content-Description: File Transfer' );
                header( "Content-Disposition: attachment; filename=\"{$fileName}\"" );
                header( 'Content-Length:' . filesize( $filePath ) );
                header( 'Content-Type: application/octet-stream' );
                header( 'Expires: 0' );
                header( 'Pragma: public' );
                readfile( $filePath );
                unlink( $filePath );

                // Stop script execution
                die();
            } else {
                $request->session()->flash( 'errorMessage', 'No record found for the specified record ID.' );
            }
        } else {
            $request->session()->flash( 'errorMessage', 'Invalid record ID provided' );
        }

        return redirect()->back();
    }
}
