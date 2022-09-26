<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class ExclusionReasonsMdl extends Model {
    /**
     * PROPERTIES
     */

    // public $table      = 'view_plmat_passers';
    // public $primaryKey = 'applicantId';
    public $table      = 'vrc_exclusion_reasons';
    public $primaryKey = 'id';
    public $timestamps = false;

    /**
     * METHODS
     */

    /**
     * ACCESSORS & MUTATORS
     */
}
