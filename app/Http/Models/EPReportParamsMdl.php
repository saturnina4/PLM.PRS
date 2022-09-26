<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class EPReportParamsMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_ep_report_params';
    public $primaryKey = 'unique_id';
    public $timestamps = false;

    /**
     * METHODS
     */

    // public function findByUuid( string $primaryKey ) : GPReportParamsMdl {
    //     return parent::find( hex2bin( $primaryKey ) );
    // }

    /**
     * ACCESSORS & MUTATORS
     */

    // Unique ID Field
    public function getUniqueIdAttribute( $value ) : string {
        return ( string ) $value;
    }

    // public function getReportIdAttribute( string $reportId ) : string {
    //     return bin2hex( $reportId );
    // }
}
