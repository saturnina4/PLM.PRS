<?php

namespace MiSAKACHi\VERACiTY\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    public function report( Exception $e ) {
        parent::report( $e );
    }

    public function render( $request, Exception $e ) {
        return parent::render( $request, $e );
    }

    protected function unauthenticated( $request, AuthenticationException $e ) {
        if( $request->expectsJson() ) {
            return response()->json( ['error' => 'Unauthenticated.'], 401 );
        } else {
            $request->session()->flash( 'errorMessage', 'You are not authenticated, please sign-in to continue.' );
        }

        return redirect()->guest( '/' );
    }

}
