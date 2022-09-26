@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Part-Time Payroll Report
@endsection

@section( 'additionalCss' )
<link href="{{ config('app.root') }}/files/css/GlobalAppStyles.css" rel="stylesheet">
<link href="{{ config('app.root') }}/files/css/GPReportFinder.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Part-Time Payroll <small>Signatories</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Part-Time Payroll</a>
    </li>
    <li>
        <a href="#">Signatories</a>
    </li>
</ol>
@endsection

@section( 'pt-menu-active' )
active
@endsection

@section( 'pt-signatories-active' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-10">
        <div class="alert alert-danger hidden no-border-radius" id="validationErrors" role="alert">
            <span class="fa fa-exclamation-triangle"></span>
            <strong>Oops! </strong>Please correct the following errors below:
            <ul>
            </ul>
        </div>
        @if( Session::has( 'successMessage' ) )
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

        <div class="box box-primary custom" id="mainForm">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Signatories
                </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" type="button"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form id="pageForm" method="post" role="form">
                    <fieldset class="form-group">
                        <div>
                            <label class="">Prepared by</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Employee Name</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                                    <select class="form-control no-border-radius" id="employee1" name="employee1" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>

                                        <optgroup label="Employees">
                                            @foreach( $empDetails as $item )
                                                <option value="{{ $item->employeeNumber }}" {{ $item->employeeNumber == $signatories[0]->employeeNumber ? 'selected' : '' }}>
                                                    {{ $item->fullName }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Position</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="fa fa-briefcase"></span></span>
                                    <input class="form-control input-group" id="position1" name="position1" required type="text" value="{{ $signatories[0]->position ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <div>
                            <label class="">Certified Correct</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Employee Name</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                                    <select class="form-control no-border-radius" id="employee2" name="employee2" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>

                                        <optgroup label="Employees">
                                            @foreach( $empDetails as $item )
                                                <option value="{{ $item->employeeNumber }}" {{ $item->employeeNumber == $signatories[1]->employeeNumber ? 'selected' : '' }}>
                                                    {{ $item->fullName }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Position</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="fa fa-briefcase"></span></span>
                                    <input class="form-control input-group" id="position2" name="position2" required type="text" value="{{ $signatories[1]->position ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <div>
                            <label class="">CERTIFIED: Funds available in the amount of P</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Employee Name</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                                    <select class="form-control no-border-radius" id="employee3" name="employee3" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>

                                        <optgroup label="Employees">
                                            @foreach( $empDetails as $item )
                                                <option value="{{ $item->employeeNumber }}" {{ $item->employeeNumber == $signatories[2]->employeeNumber ? 'selected' : '' }}>
                                                    {{ $item->fullName }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Position</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="fa fa-briefcase"></span></span>
                                    <input class="form-control input-group" id="position3" name="position3" required type="text" value="{{ $signatories[2]->position ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <div>
                            <label class="">APPROVED FOR PAYMENT:</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Employee Name</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                                    <select class="form-control no-border-radius" id="employee4" name="employee4" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>

                                        <optgroup label="Employees">
                                            @foreach( $empDetails as $item )
                                                <option value="{{ $item->employeeNumber }}" {{ $item->employeeNumber == $signatories[3]->employeeNumber ? 'selected' : '' }}>
                                                    {{ $item->fullName }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Position</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon addon-custom" id="sizing-addon2"><span class="fa fa-briefcase"></span></span>
                                    <input class="form-control input-group" id="position4" name="position4" required type="text" value="{{ $signatories[3]->position ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="box-footer">
                <button class="btn btn-primary custom pull-right" id="save" type="button"><i class="fa fa-save"></i>&nbsp;&nbsp;Save Changes</button>
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
<script type="text/javascript" src="{{ config('app.root') }}/files/js/loadingoverlay.min.js"></script>
<script>
    var mainForm = $( '#mainForm' );

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

    $( function() {
        $( '#employee1' ).select2();
        $( '#employee2' ).select2();
        $( '#employee3' ).select2();
        $( '#employee4' ).select2();

        $( '#save' ).on( 'click', function () {
            mainForm.LoadingOverlay( 'show' );

            $.ajax({
                async    : true,
                data     : $( '#pageForm' ).serialize(),
                dataType : 'json',
                encode   : true,
                headers  : {
                    'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                },
                type     : 'post',
                url      : '{{ config('app.root') }}/parttime/report/signatories'
            }).done( function( data, status, xhr ) {
                try {
                    let jsonData = JSON.parse( xhr.responseText );

                    if ( jsonData.ajaxSuccess == 'ok' ) {
                        window.location = '{{ config('app.root') }}/parttime/report/signatories';
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
                    let jsonData = JSON.parse( xhr.responseText ),
                        jsonKeys = Object.keys( jsonData );

                    var validationErrors = $( '#validationErrors' );

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
                        'Request failed. Unable to parse server response.'
                    );
                }
            });

            mainForm.LoadingOverlay( 'hide' );
        } );
    });
</script>
@endsection
