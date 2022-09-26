@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Part-Time Payroll Report
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="/files/css/GPReportFinder.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Part-Time Payroll <small>Report Summary</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Part-Time Payroll</a>
    </li>
    <li>
        <a href="#">Report Summary</a>
    </li>
</ol>
@endsection

@section( 'pt-menu-active' )
active
@endsection

@section( 'pt-summary-active' )
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
        @elseif( Session::has( 'successMessage' ) )
            <div class="alert alert-success" id="successMessage" role="alert">
                <span class="fa fa-check"></span>
                <strong>Success! </strong>
                {{ Session::get( 'successMessage' ) }}
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
                <form action="" id="pageForm" method="post" role="form">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <label>Provide the following details to view the summary</label>
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

        @if ( isset( $reportTotals ) )
            <div class="box box-primary custom">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Report Summary for
                        {{ \Carbon\Carbon::createFromDate(
                            substr( $payPeriod, 0, 4), substr( $payPeriod, 5, 2), 1
                        )->format('F, Y') }}
                    </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" type="button"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body col-md-offset-1" style="width: 80%; margin">
                    <div class="row">
                        <div class=" col-md-6">Gross Amount</div>
                        <div class="col-md-6 text-right">{{ $reportTotals->empEarnedAmount }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">EWT</div>
                        <div class="col-md-6 text-right">{{ $reportTotals->tax_ewt }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">Withholding Tax</div>
                        <div class="col-md-6 text-right">{{ $reportTotals->tax_whTax }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">Other Deductions</div>
                        <div class="col-md-6 text-right">{{ $reportTotals->otherDeductions }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">Net Amount</div>
                        <div class="col-md-6 text-right">{{ $reportTotals->empNetAmount }}</div>
                    </div>
                </div>
            </div>
        @endif
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

        $( "#selectedDepartment" ).select2();
    });
</script>
@endsection
