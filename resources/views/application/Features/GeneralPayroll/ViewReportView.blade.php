@extends( 'layouts.navMain' )

@section( 'pageTitle' )
General Payroll Report
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="/files/css/GPReportViewer.css" rel="stylesheet">
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
                            <td>{{ $departmentName }}</td>
                        </tr>
                        <tr>
                            <td style="width: 120px;"><b>PAY PERIOD</b></td>
                            <td>{{ $earningPeriod }}</td>
                        </tr>
                        <tr>
                            <td style="width: 120px;"><b>DATES COVERED</b></td>
                            <td>{{ $payPeriodRange }}</td>
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
                            <th class="table-custom">ABSENCES</th>
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
                            <th class="table-custom">MAXICARE</th>
                            <th class="table-custom">OTHER BILLS</th>
                            <th class="table-custom">NET PAY</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach( $reportData as $modelObject )
                            @if( $modelObject['isExcluded'] == 0 )
                                <tr>
                                    <td>{{ $modelObject['empNumber'] }}</td>
                                    <td>{{ $modelObject['empName'] }}</td>
                                    <td>{{ $modelObject{'empDesignation'} }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['empBaseSalary'],  2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['at_salaryDeductions'] + $modelObject['at_peraDeductions'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['empLvtPay'],      2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['empPera'],        2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['empGrossSalary'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['gsis_total'],     2 ) }}</td>
                                    <td class="table-data-right-align">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary whTaxButton edit" data-id="{{ $modelObject['unique_id'] }}" type="button">
                                                    <span class="fa fa-pencil"></span>
                                                </button>
                                            </span>
                                            <input class="form-control whTaxText" data-id="{{ $modelObject['unique_id'] }}" disabled="" style="text-align: right;"
                                                type="text" value="{{ number_format( $modelObject['tax_whTax'], 2 ) }}">
                                        </div>
                                    </td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['ded_philHealth'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['pi_total'],       2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['ded_plmPcci'],    2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['ded_landBank'],   2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['ded_philamLife'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['ded_studyGrant'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['ded_maxicare'], 2 ) }}</td>
                                    <td class="table-data-right-align">{{ number_format( $modelObject['ded_otherBillsTotal'], 2 ) }}</td>
                                    <td class="table-data-right-align" data-id="{{ $modelObject['unique_id'] }}">
                                        {{ number_format( $modelObject['empNetSalary'],   2 ) }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td>{{ $modelObject['empNumber'] }}</td>
                                    <td>{{ $modelObject['empName'] }}</td>
                                    <td>{{ $modelObject{'empDesignation'} }}</td>
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
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                    <td class="table-data-right-align"><i>EXCLUDED</i></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a class="btn btn-success no-border-radius" href="{{ config('app.root') }}/gp/report/generate"><span class="fa fa-plus"></span> New Report</a>
                <a class="btn btn-primary no-border-radius" href="{{ config('app.root') }}/gp/report/find"><span class="fa fa-search"></span> Find Report</a>
                <button class="btn btn-danger no-border-radius" id="deleteReportButton"><span class="fa fa-times"></span> Delete Report</button>
                <a class="btn btn-default no-border-radius" href="{{ config('app.root') }}/gp/report/download/id/{{ $recordId }}" id="downloadButton"><span class="fa fa-cloud-download"></span> Download Printable Format</a>
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
    $( '.whTaxButton' ).on( 'click', function () {
        var classType = '';
        if ( $( this ).hasClass( 'edit' ) ) {
            classType = 'edit';
        } else if ( $( this ).hasClass( 'save' ) ) {
            classType = 'save';
        }
        switch ( classType ) {
            case 'edit':
                $( '.whTaxText[data-id="' + $( this ).data( 'id' ) + '"]' ).prop( 'disabled', false );
                $( this ).removeClass( 'edit' )
                    .removeClass( 'btn-primary' )
                    .addClass( 'btn-success' )
                    .addClass( 'save' );
                $( this ).find( 'span' ).removeClass( 'fa fa-pencil' ).addClass( 'fa fa-check' );
                break;
            case 'save':
                textBox = $( '.whTaxText[data-id="' + $( this ).data( 'id' ) + '"]' );
                button = $( this );
                var color = 'red';
                var message = '';
                var icon = '<span class="glyphicon glyphicon-remove"></span>';
                $.ajax({
                    async    : true,
                    // cache    : true,
                    data     : {
                        'recordId' : $(this).data('id'),
                        'whTax'    : textBox.val()
                    },
                    dataType : 'json',
                    encode   : true,
                    headers  : {
                        'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                    },
                    type     : 'post',
                    url      : '{{ config('app.root') }}/gp/report/edit/whTax'
                }).done( function( data, status, xhr ) {
                    try {
                        message = data.message;
                        if ( data.hasChanges == 1 ) {
                            textBox.prop( 'disabled', true );
                            button.removeClass( 'save' )
                                .removeClass( 'btn-success' )
                                .addClass( 'btn-primary' )
                                .addClass( 'edit' );
                            button.find( 'span' ).removeClass( 'fa fa-check' ).addClass( 'fa fa-pencil' );
                            textBox.val( data.whTax );
                            textBox.css('border-color', '');
                            var td = $( 'td[data-id="' + button.data( 'id' ) + '"]' );
                            td.html( data.netSalary );
                            color = 'green';
                            icon = '<span class="glyphicon glyphicon-ok"></span>';
                        } else if ( data.hasChanges == 0 ) {
                            textBox.prop( 'disabled', true );
                            button.removeClass( 'save' )
                                .removeClass( 'btn-success' )
                                .addClass( 'btn-primary' )
                                .addClass( 'edit' );
                            button.find( 'span' ).removeClass( 'fa fa-check' ).addClass( 'fa fa-pencil' );
                            textBox.val( data.whTax );
                            textBox.css('border-color', '');
                            $( '#alertDialog' ).find( 'span' ).removeClass( 'glyphicon-remove' )
                                .addClass( 'glyphicon-ok' );
                            color = 'orange';
                            icon = '<span class="glyphicon glyphicon-ok"></span>';
                        } else {
                            textBox.css('border-color', 'red');
                        }
                    } catch( errorException ) {
                        textBox.css('border-color', 'red');
                        message = 'An error has occurred.';
                    }
                    $( '#alertDialog .modal-header' ).css( 'background-color', color );
                    $( '#alertText' ).html( icon + '&nbsp;&nbsp;' + message );
                    $( '#alertDialog' ).modal( 'show' );
                }).fail( function( xhr, status, error ) {
                    try {
                        let jsonData = JSON.parse( xhr.responseText );

                        if( typeof( jsonData.error ) === 'string' ) {
                            textBox.css('border-color', 'red');
                            message = jsonData.error;
                        } else {
                            textBox.css('border-color', 'red');
                            message = 'An error has occurred.';
                        }
                    } catch( errorException ) {
                        textBox.css('border-color', 'red');
                        message = 'An error has occurred.';
                    }
                    $( '#alertDialog .modal-header' ).css( 'background-color', color );
                    $( '#alertText' ).html( icon + '&nbsp;&nbsp;' + message );
                    $( '#alertDialog' ).modal( 'show' );
                });
                break;
        }
    });
    
    $( document ).ready( function(){
        $( '#reportTable' ).DataTable({
            scrollX : true,
            order   : [[ 1, 'asc' ]]
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
                    window.location = '{{ route( 'getGPFindReport' ) }}';
                }
            }).fail( function( xhr, status, error ) {
                alert( 'ERROR' );
            });
        }
    }

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
            let jsonData = jQuery.parseJSON( xhr.responseText );

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
