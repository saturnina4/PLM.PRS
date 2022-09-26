<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Database\Eloquent\Model;

final class PTReportSummaryMdl extends Model {
    /**
     * PROPERTIES
     */

    public $table      = 'vrc_pt_report_summary';
    public $primaryKey = 'report_id';
    public $timestamps = false;

    /**
     * METHODS
     */

    // public function findByUuid( string $primaryKey ) : GPReportSummaryMdl {
    //     return parent::find( hex2bin( $primaryKey ) );
    // }

    /**
     * ACCESSORS & MUTATORS
     */

    // Unique ID Field
    // public function getUniqueIdAttribute( string $uniqueId ) : string {
    //     return bin2hex( $uniqueId );
    // }
}
