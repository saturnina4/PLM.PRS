<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class GPReportParamsMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_gp_report_params';
    public $primaryKey = 'unique_id';
    public $timestamps = false;

    /**
     * METHODS
     */

    public function findByUuid( string $primaryKey ) : GPReportParamsMdl {
        return parent::find( hex2bin( $primaryKey ) );
    }

    /**
     * ACCESSORS & MUTATORS
     */

    // Unique ID Field
    public function getUniqueIdAttribute( string $uniqueId ) : string {
        return bin2hex( $uniqueId );
    }

    public function getReportIdAttribute( string $reportId ) : string {
        return bin2hex( $reportId );
    }
}
