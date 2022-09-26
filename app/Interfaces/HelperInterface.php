<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Interfaces;

use Illuminate\Support\Collection;

interface HelperInterface {
    public function makeOptimizedUuid( bool $hex = true ) : string;

    public function getEmployeeData( int $reportType, int $department, array $excludedEmployees = [] ) : Collection;

    public function getEmployeeDeductions( string $employeeNumber ) : Collection;

    public function getActiveTranche() : Collection;

    public function getSignatories( string $earningPeriod ) : Collection;

    public function getDepartmentDetails( int $departmentId ) : Collection;

    public function emptyInputFilter( string $value, bool $nullable = false );
}
