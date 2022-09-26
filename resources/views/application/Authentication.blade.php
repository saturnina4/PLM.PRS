@extends( 'layouts.Generic' )

@section( 'pageTitle' )
    Sign-in
@stop

@section( 'additionalCss' )
    <link rel="stylesheet" href="files/css/AuthRedirector.css">
@stop

@section( 'pageContent' )
<div id="error-message-container">
    @if( Session::has( 'errorMessage' ) )
        <div id="error-message" class="alert alert-danger" role="alert">
            <span class="fa fa-warning"></span>
            <strong>Access Denied! </strong>{{ Session::get( 'errorMessage' ) }}
        </div>
    @endif
</div>

<div class="box box-danger no-border-radius" id="login-form-container">
    <div id="brand-container">
        <img id="plm-seal" src="files/img/common/plm_seal_2014.png" alt="plm-seal-2014">
        <div>
            <p id="brand-text" class="center-text">PLM Payroll System</p>
        </div>
        <p class="center-text">Use your Official PLM Account to sign-in</p>
    </div>
    <div id="controls-container">
        <a class="btn btn-danger no-border-radius" href="{{auth::login() }}" id="signinButton"><span class="fa fa-windows"></span> Sign-in now</a>
    </div>
</div>
@stop


