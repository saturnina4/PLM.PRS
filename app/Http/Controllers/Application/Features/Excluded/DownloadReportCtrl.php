<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\Excluded;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MiSAKACHi\VERACiTY\Classes\HelperClass;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\EPSignatoriesMdl;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

final class DownloadReportCtrl extends Controller {
    protected $empDetailsModel,
              $epReportSummaryModel,
              $signatoriesModel,
              $helperClass;

    public function __construct() {
        $this->empDetailsModel      = new EmployeeDetailsMdl();
        $this->epReportSummaryModel = new EPReportSummaryMdl();
        $this->signatoriesModel     = new EPSignatoriesMdl();
        $this->helperClass          = new HelperClass();
    }

    public function getAction( Request $request, string $recordId ) {
        if ( ctype_xdigit( $recordId ) && mb_strlen( $recordId ) === 32 ) {
            $fileExt        = env( 'EP_REPORTS_FILE_EXTENSION', 'xlsm' );
            $fileFormat     = env( 'REPORTS_FILE_FORMAT', 'Excel2007' );
            // $recordsPerPage = env( 'CP_REPORTS_RECORDS_PER_PAGE' );
            $savePath       = env( 'REPORTS_SAVE_PATH' );
            $templateFile   = env( 'EP_REPORTS_TEMPLATE_FILE' );

            // Get the report data
            $reportData = $this->epReportSummaryModel
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
                // $pageCount     = ceil( $recordCount / $recordsPerPage );
                $fileName      = "EXCLUDED-[{$payPeriod}].{$fileExt}";
                $filePath      = $savePath . $fileName;
                $pageName      = 'PAGE';

                // Get the base template
                $baseTemplate  = $excelObject->getActiveSheet()->setTitle( "{$pageName} 0" );
                $clonedSheet = clone $baseTemplate;
                $excelObject->addSheet( $clonedSheet->setTitle( "{$pageName} 1" ) );
                
                $pageNumber = 1;
                
                $startingRow = 9;
                $endRow = 47;
                $rowForTotals = 49;
                $currentRow = $startingRow;
                $count = 0;
                
                $departmentName = '';
                $departmentNameChanged = false;
                
                foreach ( $reportData as $data ) {
                    // count rows for employee
                    $gsis = [
                        'gsis_lr'           => 'LR',
                        'gsis_policy'       => 'PL',
                        'gsis_consolidated' => 'CONS',
                        'gsis_emergency'    => 'EMER',
                        'gsis_education'    => 'EDUC',
                        'gsis_umidCa'       => 'UMIDCA',
                        'gsis_uoliPolicy'   => 'UOLIP',
                        'gsis_uoliLoan'     => 'UOLIL',
                        'gsis_gfal'         => 'GFAL',
                    ];
                    
                    $pagIbig = [
                        'pi_premium' => '',
                        'pi_mpl'     => 'MPL',
                        'pi_ecl'     => 'ECL',
                        'pi_mp2'     => 'MP2',
                    ];
                    
                    $others = [
                        'ded_nhmfc'      => 'NHMFC',
                        'ded_maxicare'   => 'MACI',
                        'ded_philamLife' => 'PHILAM',
                        'ded_studyGrant' => 'STUDY',
                        'ded_landBank'   => 'LB',
                        'ded_otherBills' => 'OTHER',
                    ];
                        // 'ded_nhmfc'           => round( $data['empNhmfc'] ?? 0.0, 2 ),
                        // 'ded_maxicare'        => round( $data['empMaxicare'] ?? 0.0, 2 ),
                        // 'ded_philamLife'      => round( $data['empPhilamLife'] ?? 0.0, 2 ),
                        // 'ded_studyGrant'      => round( $data['empStudyGrant'] ?? 0.0, 2 ),
                        // 'ded_landBank'        => round( $data['empLandBank'] ?? 0.0, 2 ),
                        // 'empLvtPay'           => round( $data['empLvtPay'] ?? 0.0, 2 ),empPartialPayment
                        // 'ded_otherBills'      => round( $data['empOtherBills'] ?? 0.0, 2 ),
                    
                    $employeeRowCount = 0;
                    $partialPaymentCount = 0;
                    $gsisCount = 0;
                    $pagIbigCount = 0;
                    $othersCount = 0;
                    $partialPaymentIncluded = [];
                    $gsisIncluded = [];
                    $pagIbigIncluded = [];
                    $othersIncluded = [];
                    
                    $i = 0;
                    if ( $data['empPartialPayment'] != 0 ) {
                        ++$partialPaymentCount;
                        $partialPaymentIncluded[$i++] = [ 'empPartialPayment' => '' ];
                    }
                    if ( $data['empLvtPay'] != 0 ) {
                        ++$partialPaymentCount;
                        $partialPaymentIncluded[$i++] = [ 'empLvtPay' => '' ];
                    }
                    $i = 0;
                    foreach ( $gsis as $key => $value ) {
                        if ( $data[$key] != 0 ) {
                            ++$gsisCount;
                            $gsisIncluded[$i++] = [ $key => $value ];
                        }
                    }
                    $i = 0;
                    foreach ( $pagIbig as $key => $value ) {
                        if ( $data[$key] != 0 ) {
                            ++$pagIbigCount;
                            $pagIbigIncluded[$i++] = [ $key => $value ];
                        }
                    }
                    $i = 0;
                    foreach ( $others as $key => $value ) {
                        if ( $data[$key] != 0 ) {
                            ++$othersCount;
                            $othersIncluded[$i++] = [ $key => $value ];
                        }
                    }
                    
                    $employeeRowCount = max( $partialPaymentCount, $gsisCount, $pagIbigCount, $othersCount );
                    
                    if ( $departmentName != $data['departmentName'] ) {
                        $departmentName = $data['departmentName'];
                        $departmentNameChanged = true;
                    }
                    
                    if (
                        $currentRow > $endRow ||
                        $currentRow + ( $employeeRowCount == 1 ? 0 : $employeeRowCount * 2 ) +
                            ( $departmentNameChanged ? 2 : 0 ) > $endRow
                    ) {
                        $clonedSheet = clone $baseTemplate;
                        ++$pageNumber;
                        $excelObject->addSheet( $clonedSheet->setTitle( "{$pageName} {$pageNumber}" ) );
                        $currentRow = $startingRow;
                    }
                    
                    if ( $departmentNameChanged ) {
                        $departmentNameChanged = false;
                        
                        $excelObject->setActiveSheetIndex( $pageNumber )
                            ->setCellValue( 'B' . $currentRow, $departmentName );
                         
                        $currentRow += 2;
                    }
                    
                    
                    $excelObject->setActiveSheetIndex( $pageNumber )
                        ->setCellValue( 'A' . $currentRow, ( $count + 1 ) )
                        ->setCellValue( 'B' . $currentRow, $data['empName'] )
                        ->setCellValue( 'C' . $currentRow, $data['empDesignation'] )
                        ->setCellValue( 'D' . $currentRow, $data['empBaseSalary'] )
                        ->setCellValue( 'E' . $currentRow, $data['empNoOfDays'] )
                        // ->setCellValue( 'F' . $currentRow, $data['empPartialPayment'] )
                        ->setCellValue( 'H' . $currentRow, $data['empPera'] == 0 ? '' : $data['empPera'] )
                        ->setCellValue( 'J' . $currentRow, $data['empGrossSalary'] )
                        ->setCellValue( 'K' . $currentRow, $data['ded_philHealth'] == 0 ? '' : $data['ded_philHealth'] )
                        ->setCellValue( 'L' . $currentRow, $data['tax_whTax'] == 0 ? '' : $data['tax_whTax'] )
                        ->setCellValue( 'O' . $currentRow, $data['ded_plmPcci'] == 0 ? '' : $data['ded_plmPcci'] )
                        ->setCellValue( 'R' . $currentRow, $data['at_salaryDeductions'] == 0 ? '' : $data['at_salaryDeductions'] )
                        // ->setCellValue( 'T' . $currentRow, $data['ded_otherBills'] == 0 ? '' : $data['ded_otherBills'] )
                        ->setCellValue( 'U' . $currentRow, $data['empNetSalary'] )
                        ->setCellValue( 'V' . $currentRow, ( $count + 1 ) );
                        
                    for ( $i = 0; $i < $employeeRowCount; ++$i ) {
                        if ( isset( $partialPaymentIncluded[$i] ) ) {
                            foreach ( $partialPaymentIncluded[$i] as $key => $value ) {
                                $excelObject->setActiveSheetIndex( $pageNumber )
                                    ->setCellValue( 'F' . $currentRow, $data[$key] );
                            }
                        }
                        if ( isset( $pagIbigIncluded[$i] ) ) {
                            foreach ( $pagIbigIncluded[$i] as $key => $value ) {
                                $excelObject->setActiveSheetIndex( $pageNumber )
                                    ->setCellValue( 'M' . $currentRow, $value )
                                    ->setCellValue( 'N' . $currentRow, $data[$key] );
                            }
                        }
                        if ( isset( $gsisIncluded[$i] ) ) {
                            foreach ( $gsisIncluded[$i] as $key => $value ) {
                                $excelObject->setActiveSheetIndex( $pageNumber )
                                    ->setCellValue( 'P' . $currentRow, $value )
                                    ->setCellValue( 'Q' . $currentRow, $data[$key] );
                            }
                        }
                        if ( isset( $othersIncluded[$i] ) ) {
                            foreach ( $othersIncluded[$i] as $key => $value ) {
                                $excelObject->setActiveSheetIndex( $pageNumber )
                                    ->setCellValue( 'S' . $currentRow, $value )
                                    ->setCellValue( 'T' . $currentRow, $data[$key] );
                            }
                        }
                        
                        $currentRow += 2;
                    }
                    
                    ++$count;
                }
                
                $excelObject->removeSheetByIndex( 0 );
                
                $pageCount = $pageNumber;
                
                for ( $page = 1; $page <= $pageCount; ++$page ) {
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        // Set payroll period
                        ->setCellValue( 'A5', 'Pay Period  : ' . strtoupper( $payPeriod ) )
                        // Add the date wherein the report was generated
                        ->setCellValue( 'A62', "GENERATED ON {$generatedOn}" )
                        // Set current page / total pages
                        ->setCellValue( 'W62', "[ {$page} / {$pageCount} ]" );
                        
                    if ( $page != $pageCount ) {
                        // Add the total of this page in the "Sub-Total" row
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( "A{$rowForTotals}", 'Sub-Total   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -' )
                            ->setCellValue( "J{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!J{$startingRow}:J{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!J{$startingRow}:J{$endRow}))" )
                            ->setCellValue( "K{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!K{$startingRow}:K{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!K{$startingRow}:K{$endRow}))" )
                            ->setCellValue( "L{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!L{$startingRow}:L{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!L{$startingRow}:L{$endRow}))" )
                            ->setCellValue( "N{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!N{$startingRow}:N{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!N{$startingRow}:N{$endRow}))" )
                            ->setCellValue( "O{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!O{$startingRow}:O{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!O{$startingRow}:O{$endRow}))" )
                            ->setCellValue( "Q{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!Q{$startingRow}:Q{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!Q{$startingRow}:Q{$endRow}))" )
                            ->setCellValue( "R{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!R{$startingRow}:R{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!R{$startingRow}:R{$endRow}))" )
                            ->setCellValue( "T{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!T{$startingRow}:T{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!T{$startingRow}:T{$endRow}))" )
                            ->setCellValue( "U{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$page}'!U{$startingRow}:U{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$page}'!U{$startingRow}:U{$endRow}))" );
                    } else {
                        // Add the overall total in the "Grand Total" row
                        $excelObject->setActiveSheetIndex( $page - 1 )
                            ->setCellValue( "A{$rowForTotals}", 'Grand Total   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -' )
                            ->setCellValue( "J{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!J{$startingRow}:J{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!J{$startingRow}:J{$endRow}))" )
                            ->setCellValue( "K{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!K{$startingRow}:K{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!K{$startingRow}:K{$endRow}))" )
                            ->setCellValue( "L{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!L{$startingRow}:L{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!L{$startingRow}:L{$endRow}))" )
                            ->setCellValue( "N{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!N{$startingRow}:N{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!N{$startingRow}:N{$endRow}))" )
                            ->setCellValue( "O{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!O{$startingRow}:O{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!O{$startingRow}:O{$endRow}))" )
                            ->setCellValue( "Q{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!Q{$startingRow}:Q{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!Q{$startingRow}:Q{$endRow}))" )
                            ->setCellValue( "R{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!R{$startingRow}:R{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!R{$startingRow}:R{$endRow}))" )
                            ->setCellValue( "T{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!T{$startingRow}:T{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!T{$startingRow}:T{$endRow}))" )
                            ->setCellValue( "U{$rowForTotals}", "=IF(SUM('PAGE 1:PAGE {$pageCount}'!U{$startingRow}:U{$endRow})=0,\"\",SUM('PAGE 1:PAGE {$pageCount}'!U{$startingRow}:U{$endRow}))" );

                        // $excelObject->getActiveSheet()->getStyle( 'A39' )->getFont()->setBold( true );

                        // for ( $i = 'F'; $i <= 'J' ; ++$i ) {
                            // $excelObject->getActiveSheet()->getStyle( "{$i}39" )->getFont()->setBold( true );
                            // $excelObject->getActiveSheet()->getStyle( "{$i}39" )->getBorders()->getTop()->setBorderStyle( \PHPExcel_Style_Border::BORDER_THICK );
                        // }
                    }

                    // Add the certified amount to the signatory
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'C55', "=SUM('PAGE 1:PAGE {$pageCount}'!T{$startingRow}:T{$endRow})" );

                    // Add Other Signatories
                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'A53', $signatories[0]->formalName )
                        ->setCellValue( 'A54', $signatories[0]->position );

                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'L53', $signatories[1]->formalName )
                        ->setCellValue( 'L54', $signatories[1]->position );

                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'S53', $signatories[2]->formalName )
                        ->setCellValue( 'S54', $signatories[2]->position );

                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'A59', $signatories[3]->formalName )
                        ->setCellValue( 'A60', $signatories[3]->position );

                    $excelObject->setActiveSheetIndex( $page - 1 )
                        ->setCellValue( 'L59', $signatories[4]->formalName )
                        ->setCellValue( 'L60', $signatories[4]->position );
                        
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
