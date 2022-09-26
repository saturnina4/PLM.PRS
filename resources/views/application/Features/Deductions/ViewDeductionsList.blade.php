@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Deductions List
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="/files/css/GPReportViewer.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Deductions List<small>Employee's Deductions</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Deductions List</a>
    </li>
    <li>
        <a href="#">View Deductions List</a>
    </li>
</ol>
@endsection

@section( 'deductions-menu-active' )
active
@endsection

@section( 'deductions-submenu2-active' )
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
                                <th class="table-custom">LONGEVITY PAY</th>
                                <th class="table-custom">GSIS POLICY</th>
                                <th class="table-custom">GSIS EMERGENCY</th>
                                <th class="table-custom">GSIS UMID CA</th>
                                <th class="table-custom">GSIS UOLI POLICY</th>
                                <th class="table-custom">GSIS UOLI LOAN</th>
                                <th class="table-custom">GSIS EDUCATION</th>
                                <th class="table-custom">GSIS CONSOLIDATED</th>
                                <th class="table-custom">GSIS GFAL</th>
                                <th class="table-custom">GSIS MPL</th>
                                <th class="table-custom">GSIS COMPUTER LOAN</th>
                                <th class="table-custom">LANDBANK</th>
                                <th class="table-custom">PLMPCCI</th>
                                <th class="table-custom">PHILAM</th>
                                <th class="table-custom">STUDY GRANT</th>
                                <th class="table-custom">PAGIBIG PREMIUM</th>
                                <th class="table-custom">PAGIBIG MPL</th>
                                <th class="table-custom">PAGIBIG ECL</th>
                                <th class="table-custom">PAGIBIG MP2</th>
                                <th class="table-custom">NHFMC</th>
                                <th class="table-custom">MAXICARE</th>
                                <th class="table-custom">OTHER BILLS</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach( $employeeInformation as $modelObject )
                            <tr>
                                <td>
                                    <div>
                                        <a class="btn btn-default no-border-radius action-button" href="view/id/{{ $modelObject['unique_id'] }}" role="button"><i class="fa fa-edit"></i></a>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content">
                                        {{ $modelObject['empNumber'] }}
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content">
                                        {{ $modelObject['fullName'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['lvtPay'] }}
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisPolicy'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisEmergency'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisUmidCa'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisUoliLoan'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisUoliPolicy'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisEducation'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisConsolidated'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisGfal'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisMpl'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['gsisComputerLoan'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['landBank'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['plmPcci'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['philamLife'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['studyGrant'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['pagIbigPremium'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['pagIbigMpl'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['pagIbigEcl'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['pagIbigMp2'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['nhfmc'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['maxicare'] }}
                                    </div>
                                </td>
                                <td class="table-data-right-align">
                                    <div class="cell-content-right">
                                        {{ $modelObject['otherBills'] }}
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


