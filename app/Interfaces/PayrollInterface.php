<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Interfaces;

use Illuminate\Support\Collection;

interface PayrollInterface {
    public function generalPayroll( int $department, int $reportType, string $year, string $month, array $excluded,
                                    array $overrides ) : Collection;
}
