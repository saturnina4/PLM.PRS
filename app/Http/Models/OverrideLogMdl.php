<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class OverrideLogMdl extends Model
{
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_override_log';
    public $connection = 'prs';
    public $primaryKey = 'id';
    public $timestamps = false;

    /**
     * ACCESSORS & MUTATORS
     */

    // Record ID Field
    public function setRecordIdAttribute( string $recordId ) {
        $this->attributes['record_id'] = hex2bin( $recordId );
    }
}
