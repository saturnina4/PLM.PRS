@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Excluded Payroll Report
@endsection

@section( 'additionalCss' )
<link href="{{ config('app.root') }}/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="{{ config('app.root') }}/files/css/GPReportViewer.css" rel="stylesheet">
<style type="text/css">
    .popover-danger {
        background-color: #d9534f;
        border-color: #d43f3a;
        color: white;
        container: body;
    }

    .popover-danger.right .arrow:after {
        border-right-color: #d9534f;
    }
</style>
@endsection

@section( 'pageSectionTitle' )
<h1>
    Excluded Payroll <small>Payroll Reports Viewer</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Excluded Payroll</a>
    </li>
    <li>
        <a href="#">View Reports</a>
    </li>
</ol>
@endsection

@section( 'ep-menu-active' )
active
@endsection

@section( 'ep-view-active' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-12">
        @if( Session::has( 'errorMessage' ) )
            <div class="alert alert-danger no-border-radius" id="errorMessage" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>
                {{ Session::get( 'errorMessage' ) }}
            </div>
        @elseif( Session::has( 'successMessage' ) )
            <div class="alert alert-success no-border-radius" id="successMessage" role="alert">
                <span class="fa fa-check"></span>
                <strong>Oh yeah! </strong>
                {{ Session::get( 'successMessage' ) }}
            </div>
        @endif

        <div class="box box-primary no-border-radius">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Report Details
                </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" type="button"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div>
                    <table>
                        <tr>
                            <td style="width: 120px;"><b>PAY PERIOD</b></td>
                            <td>{{ $earningPeriod }}</td>
                        </tr>
                    </table>
                    <br>
                </div>

                <table class="table table-bordered table-striped table-hover" id="reportTable">
                    <thead class="thead-inverse">
                        <tr>
                            <th class="table-custom">EMPLOYEE NUMBER</th>
                            <th class="table-custom">EMPLOYEE NAME</th>
                            <th class="table-custom">DEPARTMENT</th>
                            <th class="table-custom">DESIGNATION</th>
                            <th class="table-custom">BASE PAY</th>
                            <th class="table-custom">ABSENCES</th>
                            <th class="table-custom">LVT PAY</th>
                            <th class="table-custom">P.E.R.A.</th>
                            <th class="table-custom">GROSS PAY</th>
                            <th class="table-custom">GSIS LR</th>
                            <th class="table-custom">GSIS POLICY</th>
                            <th class="table-custom">GSIS CONSOLIDATED</th>
                            <th class="table-custom">GSIS EMERGENCY</th>
                            <th class="table-custom">GSIS UMID CA</th>
                            <th class="table-custom">GSIS UOLI POLICY</th>
                            <th class="table-custom">GSIS UOLI LOAN</th>
                            <th class="table-custom">GSIS EDUCATION</th>
                            <th class="table-custom">GSIS GFAL</th>
                            <th class="table-custom">WITHHOLDING TAX</th>
                            <th class="table-custom">PHILHEALTH</th>
                            <th class="table-custom">PAGIBIG PREMIUM</th>
                            <th class="table-custom">PAGIBIG ECL</th>
                            <th class="table-custom">PAGIBIG MPL</th>
                            <th class="table-custom">PAGIBIG MP2</th>
                            <th class="table-custom">PLMPCCI</th>
                            <th class="table-custom">LANDBANK</th>
                            <th class="table-custom">PHILAM</th>
                            <th class="table-custom">STUDY GRANT</th>
                            <th class="table-custom">NHMFC</th>
                            <th class="table-custom">MAXICARE</th>
                            <th class="table-custom">OTHER BILLS</th>
                            <th class="table-custom">NET PAY</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach( $reportData as $modelObject )
                            <tr>
                                <td>{{ $modelObject['empNumber'] }}</td>
                                <td>{{ $modelObject['empName'] }}</td>
                                <td>{{ $modelObject{'departmentName'} }}</td>
                                <td>{{ $modelObject{'empDesignation'} }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empBaseSalary'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['at_salaryDeductions'] + $modelObject['at_peraDeductions'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empLvtPay'],      2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empPera'],        2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empGrossSalary'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_lr'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_policy'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_consolidated'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_emergency'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_umidCa'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_uoliPolicy'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_uoliLoan'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_education'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_gfal'],     2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['tax_whTax'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_philHealth'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['pi_premium'],       2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['pi_ecl'],       2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['pi_mpl'],       2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['pi_mp2'],       2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_plmPcci'],    2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_landBank'],   2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_philamLife'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_studyGrant'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_nhmfc'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_maxicare'], 2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_otherBillsTotal'], 2 ) }}</td>
                                <td class="table-data-right-align" data-id="{{ $modelObject['unique_id'] }}">
                                    {{ number_format( $modelObject['empNetSalary'],   2 ) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a class="btn btn-success no-border-radius" href="{{ config('app.root') }}/excluded/report/generate"><span class="fa fa-plus"></span> New Report</a>
                <a class="btn btn-primary no-border-radius" href="{{ config('app.root') }}/excluded/report/find"><span class="fa fa-search"></span> Find Report</a>
                <button class="btn btn-danger no-border-radius" id="deleteReportButton"><span class="fa fa-times"></span> Delete Report</button>
                <a class="btn btn-default no-border-radius" href="{{ config('app.root') }}/excluded/report/download/id/{{ $recordId }}" id="downloadButton"><span class="fa fa-cloud-download"></span> Download Printable Format</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alertDialog" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="color: white;">Alert</h4>
            </div>
            <div class="modal-body">
                <div id="alertText"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script>
    $( document ).ready( function(){
        $( '#reportTable' ).DataTable({
            scrollX : true,
            order   : []
        });

        $( '#deleteReportButton' ).click( function() {
            deletePayrollReport();
        });

        $( '#sendPayslipButton' ).click( function() {
            sendPayslipViaEmail();
        });
    });

    function deletePayrollReport() {
        let confirmDelete = confirm( 'Delete this report?' );

        if( confirmDelete ) {
            // Initiate an AJAX request
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                async       : true,
                type        : 'POST',
                url         : '',
                data        : {
                    'requestId' : '{{ $recordId }}'
                },
                dataType    : 'json',
                encode      : true
            }).done( function( data, status, xhr ) {
                let jsonData = jQuery.parseJSON( xhr.responseText );

                // Reload page to reflect changes
                if( jsonData.ajaxSuccess === true ) {
                    window.location = '{{ route( 'getEPFindReport' ) }}';
                }
            }).fail( function( xhr, status, error ) {
                alert( 'ERROR' );
            });
        }
    }
</script>
@endsection
