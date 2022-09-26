<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Traits;

use Ramsey\Uuid\Uuid;

trait GeneratesUuidTrait {
    public function makeOptimizedUuid( bool $hex = true ) : string {
        $rawUuid = Uuid::uuid4()->toString();

        $optimizedUuid = ( string ) (
            substr( $rawUuid , 14, 4 ) .
            substr( $rawUuid , 9, 4 )  .
            substr( $rawUuid , 0, 8 )  .
            substr( $rawUuid , 19, 4 ) .
            substr( $rawUuid , 24 )
        );

        return ( $hex ? $optimizedUuid : hex2bin( $optimizedUuid ) );
    }
}
