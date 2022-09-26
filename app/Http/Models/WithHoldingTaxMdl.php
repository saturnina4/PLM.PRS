<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class WithHoldingTaxMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_tax_whtax1';
    public $primaryKey = 'unique_id';
    public $timestamps = false;

    /**
     * METHODS
     */

    public function findByUuid( string $primaryKey ) : WithHoldingTaxMdl {
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
