<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class SentPayslipsMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_sent_payslips';
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
}
