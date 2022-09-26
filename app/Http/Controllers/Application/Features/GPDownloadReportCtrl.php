<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features;

use Illuminate\Http\Request;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\GPReportSummaryMdl;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

final class GPDownloadReportCtrl extends Controller {
    protected $gpReportSummaryModel,
              $helperClass;

    public function __construct() {
        $this->gpReportSummaryModel = new GPReportSummaryMdl();
        $this->helperClass          = new HelperClass();
    }

    public function getAction( Request $request, string $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) === 32 ) {
            $fileExt        = env( 'REPORTS_FILE_EXTENSION', 'xlsx' );
            $fileFormat     = env( 'REPORTS_FILE_FORMAT', 'Excel2007' );
            $recordsPerPage = env( 'REPORTS_RECORDS_PER_PAGE' );
            $savePath       = env( 'REPORTS_SAVE_PATH' );
            $templateFile   = env( 'REPORTS_TEMPLATE_FILE' );

            // Get the report data
            $reportData = $this->gpReportSummaryModel
                ->where( 'report_id', hex2bin( $recordId ) )
                ->orderBy( 'empName', 'asc' )
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

                // Set the report parameters
                if( $reportData[0]->reportType === 1 ) {
                    $reportType = 'GP-REGULAR';
                } else if( $reportData[0]->reportType === 2 ) {
                    $reportType = 'GP-CASUAL';
                } else {
                    $reportType = '';
                }

                $payPeriodFrom = date_format( date_create( $reportData[0]->payPeriodFrom ), 'F d, Y' );
                $payPeriodTo   = date_format( date_create( $reportData[0]->payPeriodTo ), 'F d, Y' );
                $fileDateFrom  = date_format( date_create( $reportData[0]->payPeriodFrom ), 'Ymd' );
                $fileDateTo    = date_format( date_create( $reportData[0]->payPeriodTo ), 'Ymd' );
                $generatedOn   = $reportData[0]->date_created;
                $excelReader   = PHPExcel_IOFactory::createReader( $fileFormat );
                $excelObject   = $excelReader->load( $templateFile );
                $pageCount     = ceil( $recordCount / $recordsPerPage );
                $fileName      = "{$departmentCode}-{$reportType} [{$fileDateFrom}-{$fileDateTo}].{$fileExt}";
                $filePath      = $savePath . $fileName;
                $pageName      = 'PAGE';

                // Get the base template
                $baseTemplate  = $excelObject->getActiveSheet();

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
                        // Set payroll period
                        ->setCellValue( 'A3', 'FOR THE PERIOD OF: ' . $payPeriodFrom . ' to ' . $payPeriodTo )
                        // Set department name
                        ->setCellValue( 'A7', $departmentName )
                        // Set current page / total pages
                        ->setCellValue( 'Q53', "[ {$page} / {$pageCount} ]" );

                    // Limit number of rows per page
                    for( $cellRow = 8; $cellRow <= 32; $cellRow += 2 ) {
                        if( $reportData[$index]['isExcluded'] === 0 ) {
                            $excelObject->setActiveSheetIndex( $page - 1 )
                                ->setCellValue( 'A' . $cellRow, $reportData[$index]['empName'] )
                                ->setCellValue( 'B' . $cellRow, $reportData[$index]['empDesignation'] )
                                ->setCellValue( 'C' . $cellRow, $reportData[$index]['empBaseSalary'] )
                                ->setCellValue( 'D' . $cellRow, $reportData[$index]['empLvtPay'] )
                                ->setCellValue( 'E' . $cellRow, $reportData[$index]['empPera'] )
                                ->setCellValue( 'G' . $cellRow, $reportData[$index]['empGrossSalary'] )
                                ->setCellValue( 'F' . $cellRow, $reportData[$index]['at_salaryDeductions'] + $reportData[$index]['at_peraDeductions'] )
                                ->setCellValue( 'H' . $cellRow, $reportData[$index]['gsis_total'] )
                                ->setCellValue( 'I' . $cellRow, $reportData[$index]['tax_whTax'] )
                                ->setCellValue( 'J' . $cellRow, $reportData[$index]['ded_philHealth'] )
                                ->setCellValue( 'K' . $cellRow, $reportData[$index]['pi_total'] )
                                ->setCellValue( 'L' . $cellRow, $reportData[$index]['ded_plmPcci'] )
                                ->setCellValue( 'M' . $cellRow, $reportData[$index]['ded_landBank'] )
                                ->setCellValue( 'N' . $cellRow, $reportData[$index]['ded_maxicare'] )
                                ->setCellValue( 'O' . $cellRow, $reportData[$index]['ded_studyGrant'] )
                                ->setCellValue( 'P' . $cellRow, $reportData[$index]['ded_otherBillsTotal'] )
                                 // ->setCellValue( 'Q' . $cellRow, "= IF( ( ROUND( G{$cellRow} - SUM( H{$cellRow}:P{$cellRow} ), 0 ) / 2 ) > 0, ( ROUND( ROUND( G{$cellRow} - SUM( H{$cellRow}:P{$cellRow} ), 0 ) / 2, 0 ) ), 0 )" )
                                ->setCellValue( 'Q' . $cellRow, "= IF( ROUND( ( G{$cellRow} - ( SUM( H{$cellRow}:P{$cellRow} ) ) ) / 2, 0 ) > 0, ROUND( ( G{$cellRow} - ( SUM( H{$cellRow}:P{$cellRow} ) ) ) / 2, 0 ), 0 )" )
                                 // ->setCellValue( 'Q' . ($cellRow + 1), "= IF( ( Q{$cellRow} <> \"\" ), ( G{$cellRow} - SUM( H{$cellRow}:P{$cellRow} ) ) - Q{$cellRow}, 0 )" )
                                ->setCellValue( 'Q' . ($cellRow + 1), "= IF( Q{$cellRow} <> \"\", ( G{$cellRow} - SUM( H{$cellRow}:P{$cellRow} ) ) - Q{$cellRow}, 0 )" )
                                  ;
                        } else {
                            $excelObject->setActiveSheetIndex( $page - 1 )
                                ->mergeCells( "C{$cellRow}:Q" . ( $cellRow + 1 ) );

                            $excelObject->getActiveSheet()
                                ->getStyle( "C{$cellRow}:Q" . ( $cellRow + 1 ) )
                                ->getAlignment()
                                ->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

                            $excelObject->getActiveSheet()
                                ->setCellValue( 'A' . $cellRow, $reportData[$index]['empName'] )
                                ->setCellValue( 'B' . $cellRow, $reportData[$index]['empDesignation'] )
                                ->setCellValue( 'C' . $cellRow, strtoupper( 'EXCLUDED DUE TO SECTION 122 OF PD 1445/SECTION 7 OF COA CIRCULAR NO. 95-006 - ' . $reportData[$index]['exclusion_reason'] ) );
                        }

                        // Add the note that the report continues on the next page
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( 'A34', '*** CONTINUED ON NEXT PAGE ***' );

                        // Increment to process the next row
                        $index++;

                        // Stop if there are no more rows to reduce unnecessary cycles
                        if( ! isset( $reportData[$index] ) ){
                            // Before stopping, set the required parameters

                            /* Since we are going to break the loop we can safely modify the
                               $cellRow variable */
                            $cellRow += 2;

                            /* Add the 'NOTHING FOLLOWS' message to signify end of the report
                               This supersedes the message CONTINUED ON NEXT PAGE */
                            if( $cellRow < 34 ) {
                                $excelObject->setActiveSheetIndex( $page - 1 )
                                    ->unmergeCells( "A{$cellRow}:A" . ( $cellRow + 1 ) )
                                    ->unmergeCells( "B{$cellRow}:B" . ( $cellRow + 1 ) )
                                    ->mergeCells( "A{$cellRow}:Q" . ( $cellRow + 1 ) )
                                    ->setCellValue( "A{$cellRow}", '*** NOTHING FOLLOWS ***' );

                                $excelObject->setActiveSheetIndex( $page - 1 )
                                    ->getStyle( "A{$cellRow}:Q{$cellRow}" )
                                    ->getAlignment()
                                    ->setHorizontal( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
                            }

                            $excelObject->setActiveSheetIndex( $page - 1 )
                                ->setCellValue( 'A34', '*** END OF REPORT ***' );

                            // Finally, loop laid unto eternal rest :)
                            break;
                        }
                    }

                    // Add the running total below the "TOTALS IN THIS PAGE" row
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'C37', "=SUM('PAGE 1:PAGE {$page}'!C36)" )
                        ->setCellValue( 'D37', "=SUM('PAGE 1:PAGE {$page}'!D36)" )
                        ->setCellValue( 'E37', "=SUM('PAGE 1:PAGE {$page}'!E36)" )
                        ->setCellValue( 'F37', "=SUM('PAGE 1:PAGE {$page}'!F36)" )
                        ->setCellValue( 'G37', "=SUM('PAGE 1:PAGE {$page}'!G36)" )
                        ->setCellValue( 'H37', "=SUM('PAGE 1:PAGE {$page}'!H36)" )
                        ->setCellValue( 'I37', "=SUM('PAGE 1:PAGE {$page}'!I36)" )
                        ->setCellValue( 'J37', "=SUM('PAGE 1:PAGE {$page}'!J36)" )
                        ->setCellValue( 'K37', "=SUM('PAGE 1:PAGE {$page}'!K36)" )
                        ->setCellValue( 'L37', "=SUM('PAGE 1:PAGE {$page}'!L36)" )
                        ->setCellValue( 'M37', "=SUM('PAGE 1:PAGE {$page}'!M36)" )
                        ->setCellValue( 'N37', "=SUM('PAGE 1:PAGE {$page}'!N36)" )
                        ->setCellValue( 'O37', "=SUM('PAGE 1:PAGE {$page}'!O36)" )
                        ->setCellValue( 'P37', "=SUM('PAGE 1:PAGE {$page}'!P36)" )
                        ->setCellValue( 'Q39', "=SUM('PAGE 1:PAGE {$page}'!L39)" )
                        ->setCellValue( 'Q40', "=SUM('PAGE 1:PAGE {$page}'!L40)" )
                        ->setCellValue( 'Q41', "=SUM('PAGE 1:PAGE {$page}'!L41)" );

                    // Add the certified amount to the signatory
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'C47', "=SUM('PAGE 1:PAGE {$pageCount}'!L41)" );

                    // Add the date wherein the report was generated
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'B53', $generatedOn );

                    // Add the Department Signatory
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'A45', $departmentHead )
                        ->setCellValue( 'A46', $departmentHeadTitle );

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
