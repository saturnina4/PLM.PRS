<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\Casual;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\CPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\CPSignatoriesMdl;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

final class DownloadReportCtrl extends Controller {
    protected $empDetailsModel,
              $ptReportSummaryModel,
              $signatoriesModel,
              $helperClass;

    public function __construct() {
        $this->empDetailsModel      = new EmployeeDetailsMdl();
        $this->cpReportSummaryModel = new CPReportSummaryMdl();
        $this->signatoriesModel     = new CPSignatoriesMdl();
        $this->helperClass          = new HelperClass();
    }

    public function getAction( Request $request, string $recordId ) {
        if( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) === 32 ) {
            $fileExt        = env( 'CP_REPORTS_FILE_EXTENSION', 'xlsm' );
            $fileFormat     = env( 'REPORTS_FILE_FORMAT', 'Excel2007' );
            $recordsPerPage = env( 'CP_REPORTS_RECORDS_PER_PAGE' );
            $savePath       = env( 'REPORTS_SAVE_PATH' );
            $templateFile   = env( 'CP_REPORTS_TEMPLATE_FILE' );

            // Get the report data
            $reportData = $this->cpReportSummaryModel
                ->where( 'report_id', $recordId )
                ->orderBy( 'departmentName', 'asc' )
                ->orderBy( 'empName', 'asc' )
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
                $earningYear  = $reportData[0]->earningYear;
                $earningMonth = $reportData[0]->earningMonth;

                // Set the report parameters
                $payPeriod = \Carbon\Carbon::createFromDate( $earningYear, $earningMonth, 1 );

                if ( $reportData[0]->cutOffPeriod == 1 ) {
                    $datePeriod = '1-15';
                } else {
                    $datePeriod = '16-' . $payPeriod->endOfMonth()->format('j');   
                }
                
                $payPeriod = $payPeriod->format('F') . ' ' . $datePeriod . ', ' . $payPeriod->format('Y');

                $generatedOn   = $reportData[0]->date_created;
                $excelReader   = PHPExcel_IOFactory::createReader( $fileFormat );
                $excelObject   = $excelReader->load( $templateFile );
                $pageCount     = ceil( $recordCount / $recordsPerPage );
                $fileName      = "CASUAL-[{$payPeriod}].{$fileExt}";
                $filePath      = $savePath . $fileName;
                $pageName      = 'PAGE';

                // Get the base template
                $baseTemplate  = $excelObject->getActiveSheet()->setTitle( "{$pageName} 1" );
                
                $page = 1;
                
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
                        ->setCellValue( 'A5', 'Pay Period  : ' . strtoupper( $payPeriod ) )
                        // Add the date wherein the report was generated
                        ->setCellValue( 'A52', "GENERATED ON {$generatedOn}" )
                        // Set current page / total pages
                        ->setCellValue( 'W52', "[ {$page} / {$pageCount} ]" );

                    // Limit number of rows per page
                    for( $cellRow = 9; $cellRow <= 37; $cellRow += 2 ) {
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( 'A' . $cellRow, ( $index + 1 ) )
                            ->setCellValue( 'B' . $cellRow, $reportData[$index]['empName'] )
                            ->setCellValue( 'C' . $cellRow, $reportData[$index]['empDesignation'] )
                            ->setCellValue( 'D' . $cellRow, $reportData[$index]['empDailySalary'] )
                            ->setCellValue( 'E' . $cellRow, $reportData[$index]['empNoOfDays'] )
                            ->setCellValue( 'F' . $cellRow, $reportData[$index]['empPartialPayment'] )
                            ->setCellValue( 'J' . $cellRow, $reportData[$index]['empGrossSalary'] )
                            ->setCellValue( 'K' . $cellRow, $reportData[$index]['ded_philHealth'] == 0 ? '' : $reportData[$index]['ded_philHealth'] )
                            ->setCellValue( 'L' . $cellRow, $reportData[$index]['tax_whTax'] == 0 ? '' : $reportData[$index]['tax_whTax'] )
                            ->setCellValue( 'O' . $cellRow, $reportData[$index]['ded_plmPcci'] == 0 ? '' : $reportData[$index]['ded_plmPcci'] )
                            ->setCellValue( 'R' . $cellRow, $reportData[$index]['at_salaryDeductions'] == 0 ? '' : $reportData[$index]['at_salaryDeductions'] )
                            ->setCellValue( 'S' . $cellRow, $reportData[$index]['ded_otherBills'] == 0 ? '' : $reportData[$index]['ded_otherBills'] )
                            ->setCellValue( 'T' . $cellRow, $reportData[$index]['empNetSalary'] )
                            ->setCellValue( 'U' . $cellRow, ( $index + 1 ) );
                            
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( 'N' . $cellRow, $reportData[$index]['pi_premium'] == 0 ? '' : $reportData[$index]['pi_premium'] )
                            ->setCellValue( 'Q' . $cellRow, $reportData[$index]['gsis_lr'] == 0 ? '' : $reportData[$index]['gsis_lr'] );
                        
                        // Pag-Ibig Premium, MPL, and ECL
                        if ( $reportData[$index]['pi_premium'] == 0 ) {
                            
                        }

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
                            // ->setCellValue( 'G39', "=IF(SUM('PAGE 1:PAGE {$page}'!G9:G38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!G9:G38))" )
                            // ->setCellValue( 'H39', "=IF(SUM('PAGE 1:PAGE {$page}'!H9:H38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!H9:H38))" )
                            // ->setCellValue( 'I39', "=IF(SUM('PAGE 1:PAGE {$page}'!I9:I38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!I9:I38))" )
                            ->setCellValue( 'T39', "=IF(SUM('PAGE 1:PAGE {$page}'!J9:J38)=0,\"-       \",SUM('PAGE 1:PAGE {$page}'!J9:J38))" );
                    } else {
                        // Add the overall total in the "Grand Total" row
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( 'A39', 'Grand Total   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -' )
                            ->setCellValue( 'F39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!F9:F38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!F9:F38))" )
                            // ->setCellValue( 'G39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!G9:G38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!G9:G38))" )
                            // ->setCellValue( 'H39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!H9:H38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!H9:H38))" )
                            // ->setCellValue( 'I39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!I9:I38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!I9:I38))" )
                            ->setCellValue( 'T39', "=IF(SUM('PAGE 1:PAGE {$pageCount}'!J9:J38)=0,\"-       \",SUM('PAGE 1:PAGE {$pageCount}'!J9:J38))" );

                        $excelObject->getActiveSheet()->getStyle( 'A39' )->getFont()->setBold( true );

                        for ( $i = 'F'; $i <= 'J' ; ++$i ) {
                            $excelObject->getActiveSheet()->getStyle( "{$i}39" )->getFont()->setBold( true );
                            $excelObject->getActiveSheet()->getStyle( "{$i}39" )->getBorders()->getTop()->setBorderStyle( \PHPExcel_Style_Border::BORDER_THICK );
                        }
                    }

                    // Add the certified amount to the signatory
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'C45', "=SUM('PAGE 1:PAGE {$pageCount}'!J9:J38)" );

                    // // Add Other Signatories
                    // $excelObject->setActiveSheetIndex( $page - 1 )
                        // ->setCellValue( 'A43', $signatories[0]->formalName )
                        // ->setCellValue( 'A44', $signatories[0]->position );

                    // $excelObject->setActiveSheetIndex( $page - 1 )
                        // ->setCellValue( 'D43', $signatories[1]->formalName )
                        // ->setCellValue( 'D44', $signatories[1]->position );

                    // $excelObject->setActiveSheetIndex( $page - 1 )
                        // ->setCellValue( 'A49', $signatories[2]->formalName )
                        // ->setCellValue( 'A50', $signatories[2]->position );

                    // $excelObject->setActiveSheetIndex( $page - 1 )
                        // ->setCellValue( 'G49', $signatories[3]->formalName )
                        // ->setCellValue( 'G50', $signatories[3]->position );

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
