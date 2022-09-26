<?php
declare( STRICT_TYPES = 1 );

namespace App\Http\Controllers\Application\Authentication;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use App\Http\Controllers\Controller;
use App\Http\Models\UserModel;

final class OAuth2Ctrl extends Controller {

    public function __construct() {

    }

    public function getRedirect() {
        return Socialite::driver( 'azure' )->redirect();
    }

    public function getCallback( Request $request ) : RedirectResponse {
        try {
            $socialiteUser = Socialite::driver( 'azure' )->user();

            if( isset( $socialiteUser ) ) {
                $userModel = new UserModel();

                $userModelData = $userModel
                    ->where( 'emailAddress', $socialiteUser->email )
                    ->first();

                if( isset( $userModelData ) ) {
                    Auth::login( $userModelData );

                    // Important Session Variables
                    session()->put( 'activeUser', $userModelData );
                    // session()->put( 'activeUserId', $userModelData->employee_id );
                    session()->put( 'activeUserId', $userModelData->id );
                    session()->put( 'activeUser', $userModelData->empNumber );
                    session()->put( 'activeUserName', $socialiteUser->user['displayName'] );
                    session()->put( 'activeUserEmail', $socialiteUser->user['mail'] );
                    session()->put( 'activeUserPhoto', $socialiteUser->user['avatar'] );

                    return redirect()->intended( route( 'getDashboard' ) );
                } else {
                    $request->session()->flash( 'errorMessage', 'User not authenticated to use the system.' );
                }
            } else {
                $request->session()->flash( 'errorMessage', 'Invalid data received from the provider.' );
            }
        } catch( InvalidStateException $e ) {
            $request->session()->flash( 'errorMessage', 'Improper authentication sequence initiated.' );
        } catch( \PDOException $e ) {
            $request->session()->flash( 'errorMessage', basename( $e->getFile() ) . " [{$e->getLine()}] {$e->getMessage()} - PDO Exception." );
        } catch( \Throwable $e ) {
            $request->session()->flash( 'errorMessage', basename( $e->getFile() ) . " [{$e->getLine()}] - Throwable Exception." );
        }

        return redirect()->route( 'getIndex' );
    }
}
