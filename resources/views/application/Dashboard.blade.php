@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Dashboard
@endsection

@section( 'dashboard-menu-active' )
active
@endsection

@section( 'pageSectionTitle' )
<h1>
    Dashboard <small>Home is where you belong</small>
</h1>
@endsection

@section( 'additionalCss' )
<link href="{{ config('app.root') }}/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="{{ config('app.root') }}/files/css/Dashboard.css" rel="stylesheet">
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-dashboard"></i>Dashboard</a>
    </li>
</ol>
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-12">
        @if( Session::has( 'errorMessage' ) )
            <div class="alert alert-danger no-border-radius" id="errorMesage" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>
                {{ Session::get( 'errorMessage' ) }}
            </div>
        @else
            <div class="box box-primary no-border-radius">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Welcome to VERACiTY
                    </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" type="button"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove" type="button" ><i class="fa fa-remove"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="box-content-padding">
                        <div class="dashboard-content">
                            <p class="text-align-center"><i class="fa fa-15x fa-dashboard"></i></p>
                            <p class="text-align-center">Select an action to perform on the left panel</p>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary no-border-radius pull-right"><i class="fa fa-info-circle"></i> About Application</button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
