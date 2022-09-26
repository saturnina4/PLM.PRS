<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class PartTimeEmpDetailsMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_pt_employee_details';
    public $primaryKey = 'id';
    public $timestamps = false;

    /**
     * METHODS
     */

    /**
     * ACCESSORS & MUTATORS
     */

    /**
     * RELATIONSHIPS
     */

    public function position()
    {
        // return $this->hasOne(  );
    }
}
