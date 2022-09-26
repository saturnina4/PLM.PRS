<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\App\Authentication;

use Illuminate\Support\Facades\Auth;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;

final class NativeAuthCtrl extends Controller {

    public function __construct() {
        // TODO
    }

    public function getAction() {
        if( Auth::check() ) {
            return redirect()->route( 'getDashboard' );
        } else {
            return view( 'app.Authentication.Auth' );
        }
    }

    public function postAction() {
        // TODO
    }
}
