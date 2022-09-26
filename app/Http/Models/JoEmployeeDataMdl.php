<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class JoEmployeeDataMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_jo_employee_data';
    public $primaryKey = 'uid';
    public $timestamps = false;

    /**
     * METHODS
     */

    // TODO

    /**
     * ACCESSORS & MUTATORS
     */

    // TODO

    /**
     * RELATIONSHIPS
     */

    public function employeeData() : HasOne {
        return $this->hasOne( EmployeeDetailsMdl::class, 'employeeNumber', 'employeeNumber' );
    }
}
