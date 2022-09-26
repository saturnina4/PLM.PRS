@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Job Order Report
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="/files/css/GPReportFinder.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Job Order <small>Find Reports</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Job Order</a>
    </li>
    <li>
        <a href="#">Find Reports</a>
    </li>
</ol>
@endsection

@section( 'jobOrderMenu' )
active
@endsection

@section( 'joViewMenu' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-6">
        @if( count( $errors ) > 0 )
            <div class="alert alert-danger" id="validationErrors" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>Please correct the following errors below:
                <ul>
                    @foreach( $errors->all() as $error )
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif( Session::has( 'errorMessage' ) )
            <div class="alert alert-danger" id="errorMessage" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>
                {{ Session::get( 'errorMessage' ) }}
            </div>
        @endif

        <div class="box box-primary custom">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Search Parameters
                </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" type="button"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="find" id="pageForm" method="post" role="form">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <label>Provide the following details to view your report</label>
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <small class="text-muted">Job Order Section</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                                    <select class="form-control no-border-radius" id="jobOrderSection" name="jobOrderSection" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>
                                        <optgroup label="Job Order Section">
                                            <option value="1">Janitorial</option>
                                            <option value="2">Security</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div>
                            <small class="text-muted">Payment Period</small>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                            <select class="form-control input-custom" id="earningPeriod" name="earningPeriod" required style="width: 100%;">
                                <option selected value="">
                                    --- SELECT FROM THE LIST ---
                                </option>
                                <option value="1">1</option>
                                <!--<option value="2">2</option>-->
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div>
                            <small class="text-muted">Year & Month of</small>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input class="form-control input-group date" id="yearAndMonth" name="yearAndMonth" pattern="^([\d]{4})-(0[1-9])?(1[0-2])?$" required type="text">
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="box-footer">
                <button class="btn btn-primary custom pull-right" form="pageForm" type="submit"><i class="fa fa-send"></i> Submit</button>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'additionalJs' )
<script>
    $( document ).ready( function() {
        $( "#yearAndMonth" ).datepicker({
            format: 'yyyy-mm',
            startView: 'months',
            minViewMode: 'months'
        });

        $( "#jobOrderSection" ).select2();
        $( "#earningPeriod" ).select2();
    });
</script>
@endsection
