<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class GsisMultiplierMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_gsis_multiplier';
    public $primaryKey = 'unique_id';
    public $timestamps = false;

    /**
     * METHODS
     */

    public function findByUuid( string $primaryKey ) : GsisMultiplierMdl {
        return parent::find( hex2bin( $primaryKey ) );
    }

    /**
     * ACCESSORS & MUTATORS
     */

    // Unique ID Field
    public function getUniqueIdAttribute( string $uniqueId ) : string {
        return bin2hex( $uniqueId );
    }
}


