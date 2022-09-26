@extends( 'layouts.navMain' )

@section( 'pageTitle' )
General Payroll Report
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="/files/css/GPReportViewer.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    General Payroll <small>Payroll Reports Viewer</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>General Payroll</a>
    </li>
    <li>
        <a href="#">View Reports</a>
    </li>
</ol>
@endsection

@section( 'gp-menu-active' )
    active
@endsection

@section( 'gp-view-active' )
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
                            <td style="width: 120px;"><b>DEPARTMENT</b></td>
                            <td>{{ 'TEST' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 120px;"><b>PAY PERIOD</b></td>
                            <td>{{ 'TEST' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 120px;"><b>DATES COVERED</b></td>
                            <td>{{ 'TEST' }}</td>
                        </tr>
                    </table>
                    <br>
                </div>

                <table class="table table-bordered table-striped table-hover" id="reportTable">
                    <thead class="thead-inverse">
                        <tr>
                            <th class="table-custom">EMPLOYEE NUMBER</th>
                            <th class="table-custom">EMPLOYEE NAME</th>
                            <th class="table-custom">DESIGNATION</th>
                            <th class="table-custom">BASE PAY</th>
                            <th class="table-custom">LVT PAY</th>
                            <th class="table-custom">P.E.R.A.</th>
                            <th class="table-custom">GROSS PAY</th>
                            <th class="table-custom">TOTAL GSIS</th>
                            <th class="table-custom">WITHHOLDING TAX</th>
                            <th class="table-custom">PHILHEALTH</th>
                            <th class="table-custom">PAGIBIG</th>
                            <th class="table-custom">PLMPCCI</th>
                            <th class="table-custom">LANDBANK</th>
                            <th class="table-custom">PHILAM</th>
                            <th class="table-custom">STUDY GRANT</th>
                            <th class="table-custom">OTHER BILLS</th>
                            <th class="table-custom">NET PAY</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach( $reportData as $record )
                            @if( $record['isExcluded'] == 0 )
                                <tr>
                                    <td>{{ $record['empNumber'] }}</td>
                                    <td>{{ $record['empName'] }}</td>
                                    <td>{{ $record{'empDesignation'} }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['empBaseSalary'],  2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['empLvtPay'],      2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['empPera'],        2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['empGrossSalary'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['gsis_total'],     2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['tax_whTax'],      2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['ded_philHealth'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['pi_total'],       2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['ded_plmPcci'],    2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['ded_landBank'],   2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['ded_philamLife'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['ded_studyGrant'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['ded_otherBillsTotal'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $record['empNetSalary'],   2 ) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td>{{ $record['empNumber'] }}</td>
                                    <td>{{ $record['empName'] }}</td>
                                    <td>{{ $record{'empDesignation'} }}</td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a class="btn btn-success no-border-radius" href="/gp/report/generate"><span class="fa fa-plus"></span> New Report</a>
                <a class="btn btn-primary no-border-radius" href="/gp/report/find"><span class="fa fa-search"></span> Find Report</a>
                <a class="btn btn-default no-border-radius" href="/gp/report/download/id/{{ $recordId }}" id="downloadButton"><span class="fa fa-cloud-download"></span> Download Printable Format</a>
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
            order   : [[ 1, 'asc' ]]
        });

        $( '#sendPayslipButton' ).click( function() {
            sendPayslipViaEmail();
        });
    });

    function sendPayslipViaEmail() {
        // Initiate an AJAX request
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            async       : true,
            type        : 'POST',
            url         : '/payslip/mail/{{ $recordId }}',
            data        : {
                'requestId' : '{{ $recordId }}'
            },
            dataType    : 'json',
            encode      : true
        }).done( function( data, status, xhr ) {
            var jsonData = jQuery.parseJSON( xhr.responseText );

            // Reload page to reflect changes
            if( jsonData.ajaxSuccess === true ) {
                location.reload( true );
            }
        }).fail( function( xhr, status, error ) {
            alert( 'ERROR' );
        });
    }
</script>
@endsection
