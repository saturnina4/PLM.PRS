@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Casual Payroll Report
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
    Casual Payroll <small>Payroll Reports Viewer</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Casual Payroll</a>
    </li>
    <li>
        <a href="#">View Reports</a>
    </li>
</ol>
@endsection

@section( 'cp-menu-active' )
active
@endsection

@section( 'cp-view-active' )
active
@endsection

@section( 'pageContent' )
<div id="tableContainer" class="row">
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
                            <!--<th class="">ACTIONS</th>-->
                            <th class="table-custom text-center">EMPLOYEE NAME</th>
                            <th class="table-custom text-center">DEPARTMENT</th>
                            <th class="table-custom text-center">DESIGNATION</th>
                            <th class="table-custom text-center">RATE PER DAY</th>
                            <th class="table-custom text-center">NO. OF DAYS</th>
                            <th class="table-custom text-center">PARTIAL PAYMENT</th>
                            <th class="table-custom text-center">CLOTH. ALLOW.</th>
                            <th class="table-custom text-center">PERA</th>
                            <th class="table-custom text-center">ADDTL. COMP.</th>
                            <th class="table-custom text-center">GROSS AMOUNT</th>
                            <th class="table-custom text-center">PHILHEALTH</th>
                            <th class="table-custom text-center">W/TAX</th>
                            <th class="table-custom text-center">PAG-IBIG PREMIUM</th>
                            <th class="table-custom text-center">PAG-IBIG MPL</th>
                            <th class="table-custom text-center">PAG-IBIG ECL</th>
                            <th class="table-custom text-center">PLMCCI</th>
                            <th class="table-custom text-center">GSIS LR</th>
                            <th class="table-custom text-center">GSIS POLICY</th>
                            <th class="table-custom text-center">GSIS CONSOLIDATED</th>
                            <th class="table-custom text-center">GSIS EMERGENCY</th>
                            <th class="table-custom text-center">GSIS EDUCATION</th>
                            <th class="table-custom text-center">ABSENCES AND UNDERTIME</th>
                            <th class="table-custom text-center">OTHER</th>
                            <th class="table-custom text-center">NET AMOUNT RECEIVED</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach( $reportData as $modelObject )
                            <tr>
                                <!--<td class="text-center text-nowrap action-column-2">
                                    <button class="btn btn-flat btn-danger btnExclude" data-id="{{ $modelObject['unique_id'] }}" type="button"><span class="fa fa-remove"></span></button>
                                    <button class="btn btn-flat btn-primary btnEdit" data-id="{{ $modelObject['unique_id'] }}" type="button"><span class="fa fa-pencil"></span></button>
                                </td>-->
                                <td>{{ $modelObject['empName'] }}</td>
                                <td>{{ $modelObject{'departmentName'} }}</td>
                                <td>{{ $modelObject{'empDesignation'} }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empDailySalary'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empNoOfDays'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empPartialPayment'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ 1 }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empPera'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ 1 }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empGrossSalary'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_philHealth'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['tax_whTax'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['pi_premium'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['pi_mpl'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['pi_ecl'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_plmPcci'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_lr'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_policy'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_consolidated'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_emergency'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['gsis_education'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['at_salaryDeductions'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['ded_otherBills'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empNetSalary'],  2 ) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a class="btn btn-success no-border-radius" href="{{ config('app.root') }}/casual/report/generate"><span class="fa fa-plus"></span> New Report</a>
                <a class="btn btn-info no-border-radius" href="{{ config('app.root') }}/casual/report/find"><span class="fa fa-search"></span> Find Report</a>
                <button class="btn btn-danger no-border-radius" id="btnDeleteReport"><span class="fa fa-times"></span> Delete Report</button>
                <!--<button class="btn btn-primary no-border-radius" id="btnAddEmployee"><span class="fa fa-plus"></span> Add New Employee</button>-->
                <a class="btn btn-default no-border-radius" href="{{ config('app.root') }}/casual/report/download/id/{{ $recordId }}" id="downloadButton"><span class="fa fa-cloud-download"></span> Download Printable Format</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script type="text/javascript" src="{{ config('app.root') }}/files/js/loadingoverlay.min.js"></script>
<script>
    let plantillaTable      = $( '#plantillaTable' ),
        tableAddNewButton   = $( '#tableAddNewButton' ),
        confirmationModal   = $( '#confirmationModal' ),
        cfnModalYesBtn      = $( '#cfnModalYesBtn' ),
        cfnModalNoBtn       = $( '#cfnModalNoBtn' ),
        documentModal       = $( '.modal' ),
        emCloseButton       = $( '#errorModalCloseBtn' ),
        emRefreshButton     = $( '#errorModalRefreshBtn' ),
        eEditMessageCallout = $( '#errorEditMessageCallout' ),
        eAddMessageCallout  = $( '#errorAddMessageCallout' ),
        errorModal          = $( '#errorModal' ),
        tableCntnr          = $( '#tableContainer' ),
        addDataFrmCntnr     = $( '#addDataFormContainer' ),
        editDataFrmCntnr    = $( '#editDataFormContainer' ),
        formData            = null,
        inputModal          = $( '#inputModal' ),
        recordIdField       = $( '#recordId' ),
        resetButton         = $( '#resetButton' ),
        editDataForm        = $( '#editDataForm' ),
        addDataForm         = $( '#addDataForm' ),
        submitButton        = $( '#submitButton' ),
        vEditErrorCallout   = $( '#validationEditErrorCallout' ),
        vAddErrorCallout    = $( '#validationAddErrorCallout' );

    function number_format( yourNumber ) {
        //Seperates the components of the number
        var n= yourNumber.toString().split(".");
        //Comma-fies the first part
        n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        //Combines the two sections
        return n.join(".");
    }

    function resetEditErrorMessages() {
        vEditErrorCallout.addClass( 'hidden' );
        eEditMessageCallout.addClass( 'hidden' );
        editDataFrmCntnr.find( 'div[id$=ContainerEdit]' ).removeClass( 'has-error' );
        editDataFrmCntnr.find( 'div[id$=ErrorMsgEdit] > small' ).html( '' );
        editDataFrmCntnr.find( 'div[id$=ErrorMsgEdit]' ).addClass( 'hidden' );
    }

    function resetAddErrorMessages() {
        vAddErrorCallout.addClass( 'hidden' );
        eAddMessageCallout.addClass( 'hidden' );
        addDataFrmCntnr.find( 'div[id$=ContainerAdd]' ).removeClass( 'has-error' );
        addDataFrmCntnr.find( 'div[id$=ErrorMsgAdd] > small' ).html( '' );
        addDataFrmCntnr.find( 'div[id$=ErrorMsgAdd]' ).addClass( 'hidden' );
    }

    function showErrorEditMessageCallout( message ) {
        resetEditErrorMessages();
        eEditMessageCallout.find( 'p' ).html( message );
        eEditMessageCallout.removeClass( 'hidden' );
    }

    function showErrorAddMessageCallout( message ) {
        resetAddErrorMessages();
        eAddMessageCallout.find( 'p' ).html( message );
        eAddMessageCallout.removeClass( 'hidden' );
    }

    function resetEditForm() {
        resetEditErrorMessages();
        editDataForm.trigger( 'reset' );
        editDataForm.find( '.date' ).val( '' ).datepicker( 'update' ).trigger( 'change' );
    }

    function resetAddForm() {
        resetAddErrorMessages();
        addDataForm.trigger( 'reset' );
        addDataForm.find( '.date' ).val( '' ).datepicker( 'update' ).trigger( 'change' );
        addDataForm.find( 'select' ).trigger( 'change' );
    }

    var reportId = '{{ $recordId }}';

    $( document ).ready( function(){
        $( '#yearMonthEdit' ).datepicker( {
            format: 'yyyy-mm',
            startView: 'months',
            minViewMode: 'months'
        });

        $( '#yearMonthAdd' ).datepicker( {
            format: 'yyyy-mm',
            startView: 'months',
            minViewMode: 'months'
        });

        $( '#reportTable' ).DataTable({
            scrollX : true,
            order   : [],
            // columnDefs: [{
                // "targets": 0,
                // "className": "text-center",
                // "orderable": false
            // }],
            drawCallback: function ( settings ) {
                $( '.btnExclude' ).on( 'click', function () {
                    var recordId = $( this ).data( 'id' );
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_WARNING,
                        title: 'Exclude Employee',
                        message: 'Are you sure you want to exclude the selected employee?',
                        buttons: [{
                            label: 'Yes',
                            cssClass: 'btn btn-flat btn-primary',
                            icon: 'fa fa-check',
                            action: function( dialogRef ) {
                                excludeEmployee( recordId );
                            }
                        },{
                            label: 'Cancel',
                            cssClass: 'btn btn-flat btn-danger',
                            icon: 'fa fa-remove',
                            action: function( dialogRef ) {
                                dialogRef.close();
                            }
                        }]
                    });
                });
                
                $( '.btnEdit' ).on( 'click', function () {
                   fetchReportData( $(this).data('id') );
                });
            }
        });

        $( '#empIdAdd' ).select2();       

        $( '#btnEditSubmit' ).on( 'click',  function () {
            editReportData();
        });

        $( '#btnEditReset' ).on( 'click',  function () {
            resetEditForm();
        });

        $( '#btnAddEmployee' ).on( 'click', function () {
            resetAddForm();
            $( '#addDataModal' ).modal( 'show' );
        });

        $( '#btnAddSubmit' ).on( 'click',  function () {
            addReportData();
        });

        $( '#btnAddReset' ).on( 'click',  function () {
            resetAddForm();
        });

        $( '#btnDeleteReport' ).click( function() {
            BootstrapDialog.show({
                type: BootstrapDialog.TYPE_DANGER,
                title: "Delete Report",
                message: "Are you sure you want to delete this report?",
                buttons: [{
                    label: ' Yes',
                    cssClass: 'btn btn-danger btn-flat',
                    icon: 'fa fa-check',
                    action: function( dialogRef ) {
                        deletePayrollReport();
                    }
                }, {
                    label: ' Cancel',
                    cssClass: 'btn btn-default btn-flat',
                    icon: 'fa fa-remove',
                    action: function( dialogRef ) {
                        dialogRef.close();
                    }
                }],
                closable: true
            });
        });
    });

    function deletePayrollReport() {
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
                window.location = '{{ route( 'getCPFindReport' ) }}';
            }
        }).fail( function( xhr, status, error ) {
            alert( 'ERROR' );
        });
    }

    function showDialog(
        vType     = BootstrapDialog.TYPE_DEFAULT,
        vTitle    = "Default Title",
        vMessage  = "Default Message",
        vLabel    = " OK",
        vCssClass = "btn btn-default",
        vIcon     = "fa fa-check"
    ) {
        BootstrapDialog.show({
            type: vType,
            title: vTitle,
            message: vMessage,
            buttons: [{
                label: vLabel,
                cssClass: vCssClass,
                icon: vIcon,
                action: function( dialogRef ) {
                    dialogRef.close();
                }
            }],
            closable: false
        });
    }

    function excludeEmployee( recordId ) {
        $.ajax({
            async    : true,
            data     : {
                'recordId' : recordId,
                'reportId' : reportId
            },
            dataType : 'json',
            encode   : true,
            headers  : {
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            },
            type     : 'post',
            url      : '{{ config('app.root') }}/parttime/report/exclude'
        }).done( function( data, status, xhr ) {
            try {
                let jsonData = JSON.parse( xhr.responseText );

                if ( jsonData.ajaxSuccess == 'empty' ) {
                    window.location = '{{ config('app.root') }}/parttime/report/find';
                } else if ( jsonData.ajaxSuccess == 'ok' ) {
                    window.location = '{{ config('app.root') }}/parttime/report/view/id/' + reportId;
                } else {
                    showDialog(
                        BootstrapDialog.TYPE_DANGER,
                        "Error",
                        jsonData.ajaxSuccess
                    );
                }
            } catch( errorException ) {
                showDialog(
                    BootstrapDialog.TYPE_DANGER,
                    "Error",
                    'Request succeeded but an unspecified error occurred.'
                );
            }
        }).fail( function( xhr, status, error ) {
            try {
                let jsonData = JSON.parse( xhr.responseText );

                if( typeof( jsonData.error ) === 'string' ) {
                    showDialog(
                        BootstrapDialog.TYPE_DANGER,
                        "Error",
                        'An application error has occurred.'
                    );
                } else {
                    showDialog(
                        BootstrapDialog.TYPE_DANGER,
                        "Error",
                        'Request failed. Server sent an unknown response.'
                    );
                }
            } catch( errorException ) {
                showDialog(
                    BootstrapDialog.TYPE_DANGER,
                    "Error",
                    'Request failed. Unable to parse server response.'
                );
            }
        });
    }

    function fetchReportData( recordId ) {
        tableCntnr.LoadingOverlay( 'show' );

        // Initiate a POST AJAX request
        $.ajax({
            async    : true,
            data     : {
                'recordId' : recordId
            },
            dataType : 'json',
            encode   : true,
            headers  : {
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            },
            type     : 'post',
            url      : '{{ config('app.root') }}/parttime/report/data/fetch'
        }).done( function( data, status, xhr ) {
            tableCntnr.LoadingOverlay( 'hide' );
            try {
                let jsonData = JSON.parse( xhr.responseText ),
                    jsonKeys = Object.keys( jsonData );

                // Reset the form contents
                resetEditForm();

                // Iterate through elements & assign the values
                jsonKeys.forEach( function( keyName ) {
                    let selector = $( '#' + keyName + 'Edit' );
                    if (selector.data('datepicker')) {
                        $(selector).val(jsonData[keyName]).datepicker("update").trigger( 'change' );
                    } else {
                        $( selector ).val( jsonData[keyName] ).trigger( 'change' );
                    }
                });

                $( '#recordId' ).val( recordId );

                $( '#editDataModal' ).modal( 'show' );
            } catch( errorException ) {
                showDialog(
                    BootstrapDialog.TYPE_DANGER,
                    "Error",
                    'Request succeeded but an unspecified error occurred.'
                );
            }
        }).fail( function( xhr, status, error ) {
            tableCntnr.LoadingOverlay( 'hide' );
            try {
                let jsonData = JSON.parse( xhr.responseText );

                if( typeof( jsonData.ajaxFailure ) === 'string' ) {
                    showDialog(
                        BootstrapDialog.TYPE_DANGER,
                        "Error",
                        jsonData.ajaxFailure
                    );
                } else {
                    showDialog(
                        BootstrapDialog.TYPE_DANGER,
                        "Error",
                        'Request failed. Server sent an unknown response.'
                    );
                }
            } catch( errorException ) {
                showDialog(
                    BootstrapDialog.TYPE_DANGER,
                    "Error",
                    'Request failed. Unable to parse server response.'
                );
            }
        });
    }

    function editReportData() {
        editDataFrmCntnr.LoadingOverlay( 'show' );
        // Initiate a POST AJAX request
        $.ajax({
            async    : true,
            data     : editDataForm.serialize(),
            dataType : 'json',
            encode   : true,
            headers  : {
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            },
            type     : 'post',
            url      : '{{ config('app.root') }}/parttime/report/data/edit'
        }).done( function( data, status, xhr ) {
            editDataFrmCntnr.LoadingOverlay( 'hide' );
            try {
                let jsonData = JSON.parse( xhr.responseText );

                if ( jsonData.ajaxSuccess == 'ok' ) {
                    window.location = '{{ config('app.root') }}/parttime/report/view/id/' + reportId;
                } else {
                    showErrorEditMessageCallout( jsonData.ajaxSuccess );
                }
            } catch( errorException ) {
                showErrorEditMessageCallout( 'Request succeeded but an unspecified error occurred.' );
            }
        }).fail( function( xhr, status, error ) {
            editDataFrmCntnr.LoadingOverlay( 'hide' );
            try {
                let jsonData = JSON.parse( xhr.responseText ),
                    jsonKeys = Object.keys( jsonData );

                if( jsonData.ajaxFailure === undefined ) {
                    // Enable Form Controls
                    // disableControls( false );

                    // Reset error messages
                    resetEditErrorMessages();

                    // Show error banner
                    vEditErrorCallout.removeClass( 'hidden' );

                    // Iterate through elements and highlight fields with error
                    jsonKeys.forEach( function( keyName ) {
                        $( '#' + keyName + 'ContainerEdit' ).addClass( 'has-error' );
                        $( '#' + keyName + 'ErrorMsgEdit > small' ).html( jsonData[keyName] );
                        $( '#' + keyName + 'ErrorMsgEdit' ).removeClass( 'hidden' );
                    });
                } else if( typeof( jsonData.ajaxFailure ) === 'string' ) {
                    showErrorEditMessageCallout( jsonData.ajaxFailure );
                } else {
                    showErrorEditMessageCallout( 'Request failed. Server sent an unknown response.' );
                }
            } catch( errorException ) {
                showErrorEditMessageCallout( 'Request failed. Unable to parse server response.' );
            }
        });
    }

    function addReportData() {
        // Initiate a POST AJAX request
        $.ajax({
            async    : true,
            data     : addDataForm.serialize(),
            dataType : 'json',
            encode   : true,
            headers  : {
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            },
            type     : 'post',
            url      : '{{ config('app.root') }}/parttime/report/data/add'
        }).done( function( data, status, xhr ) {
            try {
                let jsonData = JSON.parse( xhr.responseText );

                if ( jsonData.ajaxSuccess == 'ok' ) {
                    window.location = '{{ config('app.root') }}/parttime/report/view/id/' + reportId;
                } else {
                    showErrorAddMessageCallout( jsonData.ajaxSuccess );
                }
            } catch( errorException ) {
                showErrorAddMessageCallout( 'Request succeeded but an unspecified error occurred.' );
            }
        }).fail( function( xhr, status, error ) {
            try {
                let jsonData = JSON.parse( xhr.responseText ),
                    jsonKeys = Object.keys( jsonData );

                if( jsonData.ajaxFailure === undefined ) {
                    // Reset error messages
                    resetAddErrorMessages();

                    // Show error banner
                    vAddErrorCallout.removeClass( 'hidden' );

                    // Iterate through elements and highlight fields with error
                    jsonKeys.forEach( function( keyName ) {
                        $( '#' + keyName + 'ContainerAdd' ).addClass( 'has-error' );
                        $( '#' + keyName + 'ErrorMsgAdd > small' ).html( jsonData[keyName] );
                        $( '#' + keyName + 'ErrorMsgAdd' ).removeClass( 'hidden' );
                    });
                } else if( typeof( jsonData.ajaxFailure ) === 'string' ) {
                    showErrorAddMessageCallout( jsonData.ajaxFailure );
                } else {
                    showErrorAddMessageCallout( 'Request failed. Server sent an unknown response.' );
                }
            } catch( errorException ) {
                showErrorAddMessageCallout( 'Request failed. Unable to parse server response.' );
            }
        });
    }
</script>
@endsection
