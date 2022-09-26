@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Job Order Employee Parameters | VERACiTY
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Employee Parameters
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Job Order</a>
    </li>
    <li>
        <a href="#">Employee Parameters</a>
    </li>
</ol>
@endsection

@section( 'jobOrderMenu' )
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
                <strong>Success! </strong>
                {{ Session::get( 'successMessage' ) }}
            </div>
        @endif

        <div class="box box-primary no-border-radius">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-file"></i> Parameters
                </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" type="button"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="alert-container" style="display: none;">
                    <div class="alert alert-danger no-border-radius" id="validationErrors" role="alert">
                        <span class="fa fa-exclamation-triangle"></span>
                        <strong>Oops! </strong>Please correct the following errors below
                    </div>
                </div>
                <form action="new" id="deductionsForm" method="post" name="deductionsForm">
                    <input name="mode" type="hidden" value="update">
                    <fieldset class="form-group">
                        <label>Employee Details</label>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Employee Number</small>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-address-book-o"></span></span>
                                    <input class="form-control input-group" id="employeeNumber" maxlength="16" name="employeeNumber" disabled type="text" value="{{ $joEmployeeData->employeeNumber }}">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <small class="text-muted">Employee Name</small>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-address-book-o"></span></span>
                                    <input class="form-control input-group" id="fullName" maxlength="64" name="fullName" disabled type="text" value="{{ $joEmployeeData->fullName }}">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Daily Rate</small>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-address-book-o"></span></span>
                                    <input class="form-control input-group" id="dailyRate" maxlength="16" name="dailyRate" disabled type="text" value="{{ $joEmployeeData->salaryAmount }}">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Base Parameters</label>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Days Worked</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="daysWorked" maxlength="16" name="daysWorked" type="text" value="{{ $joEmployeeData->daysWorked }}">
                                </div>
                                <small class="text-red" id="daysWorkedErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Cut-Off Earnings</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="cutOffEarnings" maxlength="16" name="cutOffEarnings" type="text" value="{{ $joEmployeeData->cutOffEarnings }}">
                                </div>
                                <small class="text-red" id="cutOffEarningsErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">PAG-IBIG Premium</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="pagIbigPremium" maxlength="16" name="pagIbigPremium" type="text" value="{{ $joEmployeeData->pagIbigPremium }}">
                                </div>
                                <small class="text-red" id="pagIbigPremiumErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Other Earnings</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="otherEarnings" maxlength="16" name="otherEarnings" type="text" value="{{ $joEmployeeData->otherEarnings }}">
                                </div>
                                <small class="text-red" id="otherEarningsErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Night Differential</label>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Ordinary Day Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="ordinaryDayHours" maxlength="16" name="ordinaryDayHours" type="text" value="{{ $joEmployeeData->ordinaryDayHours }}">
                                </div>
                                <small class="text-red" id="ordinaryDayHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Rest Day Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="restDayHours" maxlength="16" name="restDayHours" type="text" value="{{ $joEmployeeData->restDayHours }}">
                                </div>
                                <small class="text-red" id="restDayHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Special Holiday Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="specialHolidayHours" maxlength="16" name="specialHolidayHours" type="text" value="{{ $joEmployeeData->specialHolidayHours }}">
                                </div>
                                <small class="text-red" id="specialHolidayHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Rest Day & Special Holiday Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="rdAndSpecialHolidayHours" maxlength="16" name="rdAndSpecialHolidayHours" type="text" value="{{ $joEmployeeData->rdAndSpecialHolidayHours }}">
                                </div>
                                <small class="text-red" id="rdAndSpecialHolidayHoursErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Regular Holiday Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="regularHolidayHours" maxlength="16" name="regularHolidayHours" type="text" value="{{ $joEmployeeData->regularHolidayHours }}">
                                </div>
                                <small class="text-red" id="regularHolidayHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Regular & Special Holiday Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="rhAndSpecialHolidayHours" maxlength="16" name="rhAndSpecialHolidayHours" type="text" value="{{ $joEmployeeData->rhAndSpecialHolidayHours }}">
                                </div>
                                <small class="text-red" id="rhAndSpecialHolidayHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Double Holiday Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="doubleHolidayHours" maxlength="16" name="doubleHolidayHours" type="text" value="{{ $joEmployeeData->doubleHolidayHours }}">
                                </div>
                                <small class="text-red" id="doubleHolidayHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Regular & Double Holiday Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="rdAndDoubleHolidayHours" maxlength="16" name="rdAndDoubleHolidayHours" type="text" value="{{ $joEmployeeData->rdAndDoubleHolidayHours }}">
                                </div>
                                <small class="text-red" id="rdAndDoubleHolidayHoursErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Absences & Tardiness</label>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Days</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="atDays" maxlength="16" name="atDays" type="text" value="{{ $joEmployeeData->atDays }}">
                                </div>
                                <small class="text-red" id="atDaysErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="atHours" maxlength="16" name="atHours" type="text" value="{{ $joEmployeeData->atHours }}">
                                </div>
                                <small class="text-red" id="atHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Minutes</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-hashtag"></span></span>
                                    <input class="form-control input-group" id="atMinutes" maxlength="16" name="atMinutes" type="text" value="{{ $joEmployeeData->atMinutes }}">
                                </div>
                                <small class="text-red" id="atMinutesErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="box-footer">
                <button class="btn btn-primary no-border-radius pull-right" id="saveButton" type="button"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script>
    $( document ).ready( function(){
        $( '#saveButton' ).click( function() {
            saveEmployeeDeductions();
        });
    });

    // Global Scope Variables
    let alertContainer = $( '#alert-container' );
    let formElements   = $( '#deductionsForm' );
    let saveButton     = $( '#saveButton' );

    function saveEmployeeDeductions() {
        let formData = formElements.serialize();

        // Disable controls
        disableControls( true );

        // Initiate an AJAX request
        $.ajax({
            async       : true,
            cache       : true,
            data        : formData,
            dataType    : 'json',
            encode      : true,
            headers     : {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type        : 'POST',
            url         : '{{ route( 'postJoModifyEmpParams', ['recordId'=> $recordId] ) }}'
        }).done( function( data, status, xhr ) {
            let jsonData = jQuery.parseJSON( xhr.responseText );

            // Reload page to reflect changes
            if( jsonData.ajaxSuccess === true ) {
                location.reload( true );
            } else {
                showErrorDialog( 'Failed to save information. [HTML]' );
            }
        }).fail( function( xhr, status, error ) {
            let jsonData = jQuery.parseJSON( xhr.responseText );

            if( jsonData['ajaxFailure'] ) {
                showErrorDialog( jsonData['ajaxFailure'] );
            } else {
                let jsonKeys = Object.keys( jsonData );

                // Enable controls
                disableControls( false );

                // Reset error messages
                resetErrorMessages();

                // Show error banner
                $( alertContainer ).show();

                // Iterate through elements and highlight fields with error
                jsonKeys.forEach( function( keyName ) {
                    $( '#' + keyName + 'ErrorText' ).html( jsonData[keyName] );
                });
            }
        });
    }

    function disableControls( flag ) {
        $( formElements ).find( 'input' ).prop( 'disabled', flag );
        $( formElements ).find( 'select' ).prop( 'disabled', flag );
        $( saveButton ).find( 'i' ).toggleClass( 'fa-save', !flag ).toggleClass( 'fa-spinner fa-spin', flag );
        $( saveButton ).prop( 'disabled', flag );
    }

    function resetErrorMessages() {
        $( formElements ).find( 'small[id*=ErrorText]' ).html( '' );
    }

    function showErrorDialog( message ) {
        BootstrapDialog.show({
            buttons: [{
                label: 'Refresh',
                action: function( dialog ) {
                    location.reload( true );
                },
                cssClass: 'btn-danger',
                icon: 'fa fa-refresh'
            }],
            closable: false,
            message: `<p>Something went wrong with the application, please refresh the page.</p><p><b>Error Message:</b> ${message}</p>`,
            title: '<i class="fa fa-warning"></i> Oops!',
            type: BootstrapDialog.TYPE_DANGER
        });
    }
</script>
@endsection
