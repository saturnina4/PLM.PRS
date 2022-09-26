<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Interfaces;

use Illuminate\Support\Collection;

interface ComputationsInterface {
    public function gsisLr( float $baseSalary, float $atSalaryDeductions ) : Collection;

    public function withHoldingTax( float $taxableSalary, int $dependents, int $exemptionStatus ) : float;

    public function philHealth( float $baseSalary ) : array;

    public function atDeductions( int $positionType, float $baseSalary, float $peraAllowance, int $atDays = 0,
                                  int $atHours = 0, int $atMinutes = 0, int $workingDays = 22,
                                  float $teachingUnits = 0.00 ) : Collection;
}
