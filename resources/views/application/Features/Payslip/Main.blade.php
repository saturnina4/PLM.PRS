@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Send Payslips
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="/files/css/GPReportViewer.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Payslips <small>Send Payslips</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Payslips</a>
    </li>
    <li>
        <a href="#">Send Payslips</a>
    </li>
</ol>
@endsection

@section( 'payslip-menu-active' )
active
@endsection

@section( 'payslip-submenu1-active' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-8">
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
                <form id="pageForm" method="post" role="form">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <label>Provide the following details to send payslips</label>
                    <fieldset class="form-group">
                        <div>
                            <label style="font-weight: normal;">
                                <input type="radio" name="selection" checked="" value="department">&nbsp;&nbsp;&nbsp;
                                <small class="text-muted">Department</small>
                            </label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                            <select class="form-control input-custom" id="selectedDepartment" name="selectedDepartment" required style="width: 100%;">
                                <option selected value="">
                                    --- SELECT FROM THE LIST ---
                                </option>
                                <optgroup label="Administrative Departments">
                                    @foreach( $adminDeptsList as $item )
                                        <option value="{{ $item->id }}">
                                            {{ $item->deptcode . ': ' . $item->deptname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Academic Departments">
                                    @foreach( $acadDeptsList as $item )
                                        <option value="{{ $item->id }}">
                                            {{ $item->deptcode . ': ' . $item->deptname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div>
                            <label style="font-weight: normal;">
                                <input type="radio" name="selection" value="employee">&nbsp;&nbsp;&nbsp;
                                <small class="text-muted">Employee</small>
                            </label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                            <select class="form-control input-custom" disabled="" id="selectedEmployee" name="selectedEmployee" required style="width: 100%;">
                                <option selected value="">
                                    --- SELECT FROM THE LIST ---
                                </option>
                                <optgroup label="Employees">
                                    @foreach( $employeesList as $item )
                                        <option value="{{ $item->employeeNumber }}">
                                            {{ $item->fullName }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div>
                            <small class="text-muted readonly">Year & Month</small>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input class="form-control input-group date" id="yearAndMonth" name="yearAndMonth" pattern="^([\d]{4})-(0[1-9])?(1[0-2])?$" readonly="" required type="text">
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="box-footer">
                <button class="btn btn-primary custom pull-right" form="pageForm" type="submit"><i class="fa fa-send"></i> Send</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script>
    $( document ).ready( function() {
        $( "#yearAndMonth" ).datepicker({
            format     : 'yyyy-mm',
            startView   : 'months',
            minViewMode: 'months'
        });

        $( "#selectedDepartment" ).select2();
        $( "#selectedEmployee" ).select2();

        $('input[name=selection]').change(function () {
            let checkedRadio = $('input[name=selection]:checked').val();
            switch (checkedRadio) {
                case 'department':
                    $("#selectedEmployee").prop('disabled', true);
                    $("#selectedDepartment").prop('disabled', false);
                    break;
                case 'employee':
                    $("#selectedEmployee").prop('disabled', false);
                    $("#selectedDepartment").prop('disabled', true);
                    break;
            }
        });
    });
</script>
@endsection
