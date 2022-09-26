<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class EPReportDataMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_ep_report_data';
    public $primaryKey = 'unique_id';
    public $timestamps = false;

    /**
     * METHODS
     */

    // public function findByUuid( string $primaryKey ) : GPReportDataMdl {
    //     return parent::find( hex2bin( $primaryKey ) );
    // }

    /**
     * ACCESSORS & MUTATORS
     */

    // Unique ID Field
    // public function getUniqueIdAttribute( string $uniqueId ) : string {
    //     return hex2bin( $uniqueId );
    // }
}
