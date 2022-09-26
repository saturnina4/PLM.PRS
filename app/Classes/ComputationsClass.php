<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Classes;

use Illuminate\Support\Collection;
use MiSAKACHi\VERACiTY\Http\Models\GPReportSummaryMdl;
use MiSAKACHi\VERACiTY\Http\Models\GsisMultiplierMdl;
use MiSAKACHi\VERACiTY\Http\Models\PhilHealthContributionModel;
use MiSAKACHi\VERACiTY\Http\Models\WithHoldingTaxMdl;
use MiSAKACHi\VERACiTY\Interfaces\ComputationsInterface;

final class ComputationsClass implements ComputationsInterface {
    protected $gsisMultiplierModel,
              $gpReportSummaryModel,
              $philHealthContribModel,
              $withHoldingTaxModel;

    public function __construct() {
        $this->gpReportSummaryModel   = new GPReportSummaryMdl();
        $this->gsisMultiplierModel    = new GsisMultiplierMdl();
        $this->philHealthContribModel = new PhilHealthContributionModel();
        $this->withHoldingTaxModel    = new WithHoldingTaxMdl();
    }

    public function gsisLr( float $baseSalary, float $atSalaryDeductions ) : Collection {
        $employeeShare = ( float ) 0.00;
        $employerShare = ( float ) 0.00;
        $gsisLr        = ( float ) 0.00;

        try {
            $gsisMultiplierData = $this->gsisMultiplierModel->get();

            if( count( $gsisMultiplierData ) > 0 ) {
                $employeeShare = ( float ) ( $gsisMultiplierData[0]->employeeLifeContrib + $gsisMultiplierData[0]->employeeRetContrib );
                $employerShare = ( float ) ( $gsisMultiplierData[0]->employerLifeContrib + $gsisMultiplierData[0]->employerRetContrib );
                $gsisLr        = ( float ) ( ( $baseSalary - $atSalaryDeductions ) * $employeeShare );
            }

            $returnValue = [
                'gsis_employeeShare' => round( $employeeShare, 2 ),
                'gsis_employerShare' => round( $employerShare, 2 ),
                'gsis_lr'            => round( $gsisLr, 2 ),
            ];
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return collect( $returnValue );
    }

    public function withHoldingTax( float $taxableSalary, int $dependents, int $exemptionStatus ) : float {
        $returnValue = ( float ) 0.00;

        try {
            // $whTaxData = $this->withHoldingTaxModel
                // ->selectRaw( "MAX( defaultTax ) AS 'defaultTax', MAX( taxPercentage ) AS 'taxPercentage', MAX( salaryAmount ) AS salaryAmount" )
                // ->where( 'cutOff_id', env( 'TAXRATES_TAX_TABLE_CUTOFF', 4 ) )
                // ->where( 'taxStatus_id', $exemptionStatus )
                // ->where( 'dependents', $dependents )
                // ->where( 'salaryAmount', '<=', $taxableSalary )
                // ->get();

            // if( count( $whTaxData ) > 0 ) {
                // $returnValue = ( float ) ( ( $taxableSalary - $whTaxData[0]->salaryAmount ) * $whTaxData[0]->taxPercentage ) + $whTaxData[0]->defaultTax;
            // }
            $whTaxData = $this->withHoldingTaxModel
                ->selectRaw( 'compensationLevel, defaultTax, taxPercentage' )
                ->where( 'cutOff_id', env( 'TAXRATES_TAX_TABLE_CUTOFF', 4 ) )
                // ->where( 'taxStatus_id', $exemptionStatus )
                // ->where( 'dependents', $dependents )
                ->where( 'compensationLevel', '<', $taxableSalary )
                ->orderBy( 'compensationLevel', 'desc' )
                ->get();

            if( count( $whTaxData ) > 0 ) {
                $returnValue = ( float ) ( ( $taxableSalary - $whTaxData[0]->compensationLevel ) * $whTaxData[0]->taxPercentage ) + $whTaxData[0]->defaultTax;
            }
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return round( $returnValue, 2 );
    }

    public function partTimeWithHoldingTax( float $taxableSalary, string $empNumber, string $yearMonth ) {
        $returnValue = ( float ) 0.00;

        try {
            $month = substr( $yearMonth, 5, 2 );
            $year  = substr( $yearMonth, 0, 4 );

            $gpData = $this->gpReportSummaryModel
                ->select( 'tax_taxableSalary' )
                ->where( 'empNumber', $empNumber )
                ->where( 'earningYear', $year )
                ->where( 'earningMonth', $month )
                ->get();

            if( count( $gpData ) > 0 ) {
                // $whTaxData = $this->withHoldingTaxModel
                    // ->selectRaw( "MAX( taxPercentage ) AS 'taxPercentage'" )
                    // ->where( 'cutOff_id', env( 'TAXRATES_TAX_TABLE_CUTOFF', 4 ) )
                    // ->where( 'taxStatus_id', $gpData[0]->tax_exemptionStatus )
                    // ->where( 'dependents', $gpData[0]->tax_dependents )
                    // ->where( 'salaryAmount', '<=', $gpData[0]->tax_taxableSalary )
                    // ->get();
                $whTaxData = $this->withHoldingTaxModel
                    ->selectRaw( 'taxPercentage' )
                    ->where( 'cutOff_id', env( 'TAXRATES_TAX_TABLE_CUTOFF', 4 ) )
                    ->where( 'compensationLevel', '<', $gpData[0]->tax_taxableSalary )
                    ->orderBy( 'compensationLevel', 'desc' )
                    ->get();

                if( count( $whTaxData ) > 0 ) {
                    $returnValue = ( float ) ( $taxableSalary * $whTaxData[0]->taxPercentage );
                }
            } else {
                return 'No record found in the regular general payroll.';
            }
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( $t->getMessage() );
        }

        return round( $returnValue, 2 );
    }

    public function philHealth( float $baseSalary ) : array {
        // $returnValue = ( float ) 0.00;
        $returnValues = [ ( float ) 0.00, ( float ) 0.00 ];

        try {
            // $philHealthContribData = ( float ) $this->philHealthContribModel
                // ->where( 'salary_base', '<=', $baseSalary )
                // ->max( 'employee_share' );

            // if( isset( $philHealthContribData ) ) {
                // $returnValue = ( float ) $philHealthContribData;
            // }
            if ( $baseSalary <= 10000 ) {
                // $returnValue = 137.50;
                //$returnValues = [ ( float ) 175.00, ( float ) 175.00 ];
				$returnValues = [ ( float ) 200.00, ( float ) 200.00 ];
                // $returnValues = [ ( float ) 150.00, ( float ) 150.00 ];
            // } elseif ( $baseSalary >= 70000 ) {
           // } elseif ( $baseSalary >= 60000 ) {
			} elseif ( $baseSalary >= 80000 ) {	
                // $returnValue = 550.00;
                // $returnValues = [ ( float ) 1225.00, ( float ) 1225.00 ];
				//$returnValues = [ ( float ) 900.00, ( float ) 900.00 ];
                $returnValues = [ ( float ) 1600.00, ( float ) 1600.00 ];
            } else {
                // $contribution = round( $baseSalary * 0.035, 2 );
                //$contribution = round( $baseSalary * 0.030, 2 );
				$contribution = round( $baseSalary * 0.040, 2 );
                // $contribution = round( $baseSalary * 0.03, 2 );
                // $returnValue = bcdiv( ( string ) ( bcdiv( ( string ) ( $baseSalary * 0.0275 ), '1', 2 ) / 2 ), '1', 2 );
                $employerShare = round( $contribution / 2, 2 );
                $personalShare = $contribution - $employerShare;
                $returnValues = [ ( float ) $personalShare, ( float ) $employerShare ];
            }
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }

        return $returnValues;
    }

    public function atDeductions( int $positionType, float $baseSalary, float $peraAllowance, int $atDays = 0,
                                  int $atHours = 0, int $atMinutes = 0, int $workingDays = 22,
                                  float $teachingUnits = 0.00 ) : Collection {
        /* Refer to the table 'itg_positions' & 'itg_position_types' @ database
           plm_hris_integrity to determine the position types. */

        try {
			$atDaysAmount      = ( float ) ( ( $baseSalary / $workingDays ) * $atDays );
			$atHoursAmount     = ( float ) ( ( ( $baseSalary / $workingDays ) / 8 ) * $atHours );
			$atMinutesAmount   = ( float ) ( ( ( ( $baseSalary / $workingDays ) / 8 ) / 60 ) * $atMinutes );
			$atDeductions      = ( float ) ( $atDaysAmount + $atHoursAmount + $atMinutesAmount );
			$peraDaysAmount    = ( float ) ( ( $peraAllowance / $workingDays ) * $atDays );
			$peraHoursAmount   = ( float ) ( ( ( $peraAllowance / $workingDays ) / 8 ) * $atHours );
			$peraMinutesAmount = ( float ) ( ( ( ( $peraAllowance / $workingDays ) / 8 ) / 60 ) * $atMinutes );
			$peraDeductions    = ( float ) ( $peraDaysAmount + $peraHoursAmount + $peraMinutesAmount );

            return collect([
                'atDeductions'   => round( $atDeductions, 2 ),
                'peraDeductions' => round( $peraDeductions, 2 )
            ]);
        } catch( \PDOException $p ) {
            throw new \PDOException( basename( $p->getFile() ) . " [{$p->getLine()}] -> {$p->getMessage()}" );
        } catch( \Throwable $t ) {
            throw new \Exception( basename( $t->getFile() ) . " [{$t->getLine()}] -> {$t->getMessage()}" );
        }
    }
}
