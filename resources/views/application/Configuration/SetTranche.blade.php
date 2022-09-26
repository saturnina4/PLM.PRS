@extends( 'layouts.Nav-Settings' )

@section( 'pageTitle' )
View Tranche
@endsection

@section( 'additionalCss' )
<link href="/files/css/SetTranche.css" rel="stylesheet">
@endsection

@section( 'tranche-menu-active' )
active
@endsection

@section( 'tranche-submenu2-active' )
active
@endsection

@section( 'pageSectionTitle' )
<h1>
    Settings <small>Web Application configuration can be found here</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-gear"></i>Settings</a>
    </li>
</ol>
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-6">
        @if( count( $errors ) > 0 )
            <div class="alert alert-danger custom" id="validationErrors" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>Please correct the following errors below:
                <ul>
                    @foreach( $errors->all() as $error )
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif( Session::has( 'errorMessage' ) )
            <div class="alert alert-danger custom" id="errorMessage" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>
                {{ Session::get( 'errorMessage' ) }}
            </div>
        @elseif( Session::has( 'successMessage' ) )
            <div class="alert alert-success custom" id="successMessage" role="alert">
                <span class="fa fa-check"></span>
                <strong>Oh yeah! </strong>
                {{ Session::get( 'successMessage' ) }}
            </div>
        @endif
        <div class="box box-primary custom">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Active Tranche
                </h3>
            </div>
            <div class="box-body">
                <form action="set" id="pageForm" method="post" role="form">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <label>Tranche used by the application when generating reports</label>
                    <fieldset class="form-group">
                        <div>
                            <small class="text-muted">Tranche Version</small>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon custom" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                            <select class="form-control custom" id="activeTrancheVersion" name="activeTrancheVersion" required="">
                                <option value="">
                                    --- SELECT FROM THE LIST ---
                                </option>
                                @if( isset( $activeTrancheVersion ) )
                                    <option selected value="{{ $activeTrancheVersion }}">
                                        {{ $activeTrancheVersion }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div>
                            <small class="text-muted">Tranche</small>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon custom" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                            <select class="form-control custom" id="activeTranche" name="activeTranche" required="">
                                <option value="">
                                    --- SELECT FROM THE LIST ---
                                </option>
                                @if( isset( $activeTranche ) )
                                    <option {{ ( $activeTranche === '1' ? 'selected' : '' ) }} value="1">1</option>
                                    <option {{ ( $activeTranche === '2' ? 'selected' : '' ) }} value="2">2</option>
                                    <option {{ ( $activeTranche === '3' ? 'selected' : '' ) }} value="3">3</option>
                                    <option {{ ( $activeTranche === '4' ? 'selected' : '' ) }} value="4">4</option>
                                @endif
                            </select>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="box-footer">
                <button class="btn btn-primary custom" form="pageForm" type="submit"><i class="fa fa-send"></i> Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection
