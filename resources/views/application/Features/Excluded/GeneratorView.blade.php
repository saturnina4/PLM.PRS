@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Excluded Payroll
@endsection

@section( 'additionalCss' )
<link rel="stylesheet" href="{{ config('app.root') }}/files/css/GlobalAppStyles.css">
<link rel="stylesheet" href="{{ config('app.root') }}/files/css/GPReportGenerator.css">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Excluded Payroll <small>Generate Report</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Excluded Payroll</a>
    </li>
    <li>
        <a href="#">New Report</a>
    </li>
</ol>
@endsection


@section( 'ep-menu-active' )
active
@endsection

@section( 'ep-gen-active' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-6">
        <div class="alert alert-danger hidden no-border-radius" id="validationErrors" role="alert">
            <span class="fa fa-exclamation-triangle"></span>
            <strong>Oops! </strong>Please correct the following errors below:
            <ul>
            </ul>
        </div>
        <div class="alert alert-danger hidden no-border-radius" id="errorMessage" role="alert">
            <span class="fa fa-exclamation-triangle"></span>
            <strong>Something went amiss. </strong>
        </div>
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
                <form action="" id="mainForm" method="post" name="mainForm">
                    <fieldset class="form-group">
                        <label for="paymentDate">Generate Report for the specified month and cut-off period</label>

                        <div class="row">
                            <div class="col-md-9">
                                <div>
                                    <small class="text-muted">For the year & month of</small>
                                </div>

                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-calendar"></span></span>
                                    <input class="form-control input-group date" id="paymentDate" name="paymentDate" pattern="^([\d]{4})-(0[1-9])?(1[0-2])?$" required type="text">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-9">
                                <div>
                                    <small class="text-muted">Cut-Off Period</small>
                                </div>

                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                                    <select class="form-control no-border-radius" id="cutOffPeriod" name="cutOffPeriod" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>
                                        <option value="1">First Quincena</option>
                                        <option value="2">Second Quincena</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="box-footer">
                <button class="btn btn-success no-border-radius pull-right" id="submitBtn" type="button"><span class="fa fa-file"></span> Generate Report</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script type="text/javascript" src="{{ config('app.root') }}/files/js/loadingoverlay.min.js"></script>
<script>
    // Global Scope Variables
    let regexPattern     = new RegExp( "^([0-9]{1,16})+$" ),
        paymentDate      = $( '#paymentDate' ),
        cutOffPeriod     = $( '#cutOffPeriod' ),
        earningPeriod    = $( '#earningPeriod' ),
        mainForm         = $( '#mainForm' ),
        submitBtn        = $( '#submitBtn' ),
        validationErrors = $( '#validationErrors' ),
        empTable         = $( '#empTable' );

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

    $( document ).ready( function() {
        // Configure Addons
        paymentDate.datepicker( {
            format     : 'yyyy-mm',
            startView  : 'months',
            minViewMode: 'months',
            autoclose  : true
        } );

        empTable.DataTable( {
            scrollX: true,
            autoWidth: true,
            ordering: false,
            aoColumns: [
                { 'sWidth': '80%' },
                { 'sWidth': '20%' }
            ]
        } );

        cutOffPeriod.select2();

        submitBtn.on( 'click', function () {
            var table1 = empTable.DataTable();

            let formData = paymentDate.serialize() + '&' +
                           cutOffPeriod.serialize() + '&' +
                           table1.$( 'input' ).serialize();

            // Reset error messages
            validationErrors.find( 'ul' ).empty();

            // Hide all previously shown modal windows
            // documentModal.modal( 'hide' );
            // Show error banner
            validationErrors.addClass( 'hidden' );

            // Initiate a POST AJAX request
            $.ajax({
                async    : true,
                cache    : true,
                data     : formData,
                dataType : 'json',
                encode   : true,
                headers  : {
                    'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                },
                type     : 'post',
                url      : ''
            }).done( function( data, status, xhr ) {
                try {
                    let jsonData = JSON.parse( xhr.responseText );

                    if( jsonData.recordId !== undefined ) {
                        window.location = '{{ config('app.root') }}/excluded/report/view/id/' + jsonData.recordId;
                    } else if( typeof( jsonData.ajaxFailure ) === 'string' ) {
                        showDialog(
                            BootstrapDialog.TYPE_DANGER,
                            "Error",
                            jsonData.ajaxFailure
                        );
                    } else {
                        showDialog(
                            BootstrapDialog.TYPE_DANGER,
                            "Error",
                            'Request completed but an incorrect response was received.'
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
                        let jsonData = JSON.parse( xhr.responseText ),
                            jsonKeys = Object.keys( jsonData );

                        if( jsonData.ajaxFailure === undefined ) {
                            // Reset error messages
                            validationErrors.find( 'ul' ).empty();

                            // Hide all previously shown modal windows
                            // documentModal.modal( 'hide' );
                            // Show error banner
                            validationErrors.removeClass( 'hidden' );

                            var set = new Set();

                            jsonKeys.forEach( function( keyName ) {
                                set.add( String( jsonData[keyName] ) );
                            });

                            for ( let item of set ) {
                                validationErrors.find( 'ul' ).append( '<li>' + item + '</li>' );
                            }

                            $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );

                        } else if( typeof( jsonData.ajaxFailure ) === 'string' ) {
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
                            'Request failed. Unable to parse server response.' + errorException
                        );
                    }
            });
        })
    });
</script>
@endsection
