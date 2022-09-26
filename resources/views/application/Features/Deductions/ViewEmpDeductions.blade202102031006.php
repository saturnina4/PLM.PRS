@extends( 'layouts.navMain' )

@section( 'pageTitle' )
View Employee Deductions
@endsection

@section( 'additionalCss' )
<link href="/files/css/GlobalAppStyles.css" rel="stylesheet">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Deductions <small>Deductions Viewer</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Deductions</a>
    </li>
    <li>
        <a href="#">View Deductions</a>
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
                    <i class="fa fa-file"></i> Employee Deductions
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
                                    <input class="form-control input-group" id="employeeNumber" maxlength="16" name="employeeNumber" disabled type="text" value="{{ $employeeDeductions[0]->empNumber }}">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <small class="text-muted">Employee Name</small>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-address-book-o"></span></span>
                                    <input class="form-control input-group" id="fullName" maxlength="64" name="fullName" disabled type="text" value="{{ $employeeDeductions[0]->fullName }}">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Deductions</label>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">GSIS Policy</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisPolicy" maxlength="16" name="gsisPolicy" type="text" value="{{ $employeeDeductions[0]->gsisPolicy }}">
                                </div>
                                <small class="text-red" id="gsisPolicyErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">GSIS Emergency</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisEmergency" maxlength="16" name="gsisEmergency" type="text" value="{{ $employeeDeductions[0]->gsisEmergency }}">
                                </div>
                                <small class="text-red" id="gsisEmergencyErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">GSIS UMID CA</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisUmidCa" maxlength="16" name="gsisUmidCa" type="text" value="{{ $employeeDeductions[0]->gsisUmidCa }}">
                                </div>
                                <small class="text-red" id="gsisUmidCaErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">GSIS UOLI Loan</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisUoliLoan" maxlength="16" name="gsisUoliLoan" type="text" value="{{ $employeeDeductions[0]->gsisUoliLoan }}">
                                </div>
                                <small class="text-red" id="gsisUoliLoanErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">GSIS UOLI Policy</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisUoliPolicy" maxlength="16" name="gsisUoliPolicy" type="text" value="{{ $employeeDeductions[0]->gsisUoliPolicy }}">
                                </div>
                                <small class="text-red" id="gsisUoliPolicyErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">GSIS Education</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisEducation" maxlength="16" name="gsisEducation" type="text" value="{{ $employeeDeductions[0]->gsisEducation }}">
                                </div>
                                <small class="text-red" id="gsisEducationErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">GSIS Consolidated</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisConsolidated" maxlength="16" name="gsisConsolidated" type="text" value="{{ $employeeDeductions[0]->gsisConsolidated }}">
                                </div>
                                <small class="text-red" id="gsisConsolidatedErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">GSIS GFAL</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="gsisGfal" maxlength="16" name="gsisGfal" type="text" value="{{ $employeeDeductions[0]->gsisGfal }}">
                                </div>
                                <small class="text-red" id="gsisGfalErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Landbank</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="landBank" maxlength="16" name="landBank" type="text" value="{{ $employeeDeductions[0]->landBank }}">
                                </div>
                                <small class="text-red" id="landBankErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">PLM-PCCI</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="plmPcci" maxlength="16" name="plmPcci" type="text" value="{{ $employeeDeductions[0]->plmPcci }}">
                                </div>
                                <small class="text-red" id="plmPcciErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">PhilamLife</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="philamLife" maxlength="16" name="philamLife" type="text" value="{{ $employeeDeductions[0]->philamLife }}">
                                </div>
                                <small class="text-red" id="philamLifeErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Study Grant</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="studyGrant" maxlength="16" name="studyGrant" type="text" value="{{ $employeeDeductions[0]->studyGrant }}">
                                </div>
                                <small class="text-red" id="studyGrantErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">PAG-IBIG MPL</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="pagIbigMpl" maxlength="16" name="pagIbigMpl" type="text" value="{{ $employeeDeductions[0]->pagIbigMpl }}">
                                </div>
                                <small class="text-red" id="pagIbigMplErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">PAG-IBIG ECL</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="pagIbigEcl" maxlength="16" name="pagIbigEcl" type="text" value="{{ $employeeDeductions[0]->pagIbigEcl }}">
                                </div>
                                <small class="text-red" id="pagIbigEclErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">PAG-IBIG Premium</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="pagIbigPremium" maxlength="16" name="pagIbigPremium" type="text" value="{{ $employeeDeductions[0]->pagIbigPremium }}">
                                </div>
                                <small class="text-red" id="pagIbigPremiumErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">PAG-IBIG MP2</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="pagIbigMp2" maxlength="16" name="pagIbigMp2" type="text" value="{{ $employeeDeductions[0]->pagIbigMp2 }}">
                                </div>
                                <small class="text-red" id="pagIbigMp2ErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">NHMFC</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="nhmfc" maxlength="16" name="nhmfc" type="text" value="{{ $employeeDeductions[0]->nhmfc }}">
                                </div>
                                <small class="text-red" id="nhmfcErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Maxicare</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="maxicare" maxlength="16" name="maxicare" type="text" value="{{ $employeeDeductions[0]->maxicare }}">
                                </div>
                                <small class="text-red" id="maxicareErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Other Bills</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="otherBills" maxlength="16" name="otherBills" type="text" value="{{ $employeeDeductions[0]->otherBills }}">
                                </div>
                                <small class="text-red" id="otherBillsErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Longevity Pay</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="lvtPay" maxlength="16" name="lvtPay" type="text" value="{{ $employeeDeductions[0]->lvtPay }}">
                                </div>
                                <small class="text-red" id="lvtPayErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Absences & Tardiness</label>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Days</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="atDays" maxlength="16" name="atDays" type="text" value="{{ $employeeDeductions[0]->atDays }}">
                                </div>
                                <small class="text-red" id="atDaysErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Hours</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="atHours" maxlength="16" name="atHours" type="text" value="{{ $employeeDeductions[0]->atHours }}">
                                </div>
                                <small class="text-red" id="atHoursErrorText"></small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Minutes</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="atMinutes" maxlength="16" name="atMinutes" type="text" value="{{ $employeeDeductions[0]->atMinutes }}">
                                </div>
                                <small class="text-red" id="atMinutesErrorText"></small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Overrides</label>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Manual Withholding Tax</small>
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2"><span class="fa fa-address-card-o"></span></span>
                                    <input class="form-control input-group" id="manualWhTax" maxlength="16" name="manualWhTax" type="text" value="{{ $employeeDeductions[0]->manualWhTax }}">
                                </div>
                                <small class="text-red" id="manualWhTaxErrorText"></small>
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
            url         : '{{ $recordId }}'
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
