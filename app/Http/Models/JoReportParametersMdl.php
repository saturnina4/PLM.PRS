<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class JoReportParametersMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_jo_report_parameters';
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

    public function reportData() : HasMany {
        return $this->hasMany( JoReportDataMdl::class, 'jrp_uid', 'uid' );
    }
}
