@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Employee Parameters
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="/files/css/GPReportViewer.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Employee Parameters
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Employee Parameters</a>
    </li>
    <li>
        <a href="#">View Employee Parameters</a>
    </li>
</ol>
@endsection

@section( 'jobOrderMenu' )
active
@endsection

@section( 'joParametersMenu' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-12">
        @if( Session::has( 'errorMessage' ) )
            <div class="alert alert-danger" id="errorMessage" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>
                {{ Session::get( 'errorMessage' ) }}
            </div>
        @else
            <div class="box box-primary no-border-radius">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-file"></i> Details
                    </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" type="button"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" id="reportTable">
                        <thead class="thead-inverse">
                            <tr>
                                <th>ACTION</th>
                                <th class="table-custom">EMPLOYEE NUMBER</th>
                                <th class="table-custom">EMPLOYEE NAME</th>
                                <th class="table-custom">DAYS WORKED</th>
                                <th class="table-custom">HOURS WITH DIFF PAY</th>
                                <th class="table-custom">CUT-OFF EARNINGS</th>
                                <th class="table-custom">OTHER EARNINGS</th>
                                <th class="table-custom">PAG-IBIG PREMIUM</th>
                                <th class="table-custom">REMARKS</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach( $employeeData as $record )
                            <tr>
                                <td>
                                    <div>
                                        <a class="btn btn-default no-border-radius action-button" href="{{ route( 'getJoModifyEmpParams', ['recordId' => $record->uid] ) }}" role="button"><i class="fa fa-edit"></i></a>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content">
                                        {{ $record->employeeNumber }}
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content">
                                        {{ $record->fullName }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $record->daysWorked }}
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content-right">
                                        {{ 'TEST' }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $record->cutOffEarnings }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $record->otherEarnings }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $record->pagIbigPremium }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $record->remarks }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <a class="btn btn-primary btn-no-border-radius pull-right" href="/deductions/add"><span class="fa fa-plus"></span> Add New Deduction</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script>
    $( document ).ready( function(){
        $( '#reportTable' ).DataTable({
            'scrollX': true
        });
    });
</script>
@endsection
