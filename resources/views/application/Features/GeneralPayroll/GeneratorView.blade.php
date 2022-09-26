@extends( 'layouts.navMain' )

@section( 'pageTitle' )
General Payroll
@endsection

@section( 'additionalCss' )
<link rel="stylesheet" href="/files/css/GlobalAppStyles.css">
<link rel="stylesheet" href="/files/css/GPReportGenerator.css">
@endsection

@section( 'pageSectionTitle' )
<h1>
    General Payroll <small>View Reports</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>General Payroll</a>
    </li>
    <li>
        <a href="#">New Report</a>
    </li>
</ol>
@endsection


@section( 'gp-menu-active' )
active
@endsection

@section( 'gp-gen-active' )
active
@endsection

@section( 'gp-gen-regular-active' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-8">
        @if( count( $errors ) > 0 )
            <div class="alert alert-danger no-border-radius" id="validationErrors" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Oops! </strong>Please correct the following errors below:
                <ul>
                    @foreach( $errors->all() as $error )
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif( Session::has( 'errorMessage' ) )
            <div class="alert alert-danger no-border-radius" id="errorMessage" role="alert">
                <span class="fa fa-exclamation-triangle"></span>
                <strong>Something went amiss. </strong>
                {{ Session::get( 'errorMessage' ) }}
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
                <form action="generate" id="mainForm" method="post" name="mainForm">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <fieldset class="form-group">
                        <label for="reportType">Generate Report for the specified employee type</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Report Type</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                                    <select class="form-control no-border-radius" id="reportType" name="reportType" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>
                                        <option value="1">Permanent</option>
                                        <option value="2">Casual</option>
                                        <option value="0">Excluded</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="feedbackType">Generate Report for the specified month</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">For the year & month of</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-calendar"></span></span>
                                    <input class="form-control input-group date" id="paymentDate" name="paymentDate" pattern="^([\d]{4})-(0[1-9])?(1[0-2])?$" required type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">Payment Period</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                                    <select class="form-control no-border-radius" id="earningPeriod" name="earningPeriod" required style="width: 100%;">
                                        <option selected value="">
                                            --- SELECT FROM THE LIST ---
                                        </option>
                                        <option value="1">1</option>
                                        <!--<option value="2">2</option>-->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="feedbackType">Cut-Off Period</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">From</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-calendar"></span></span>
                                    <input class="form-control input-group date" id="payPeriodFrom" name="payPeriodFrom" required type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <small class="text-muted">To</small>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-calendar"></span></span>
                                    <input class="form-control input-group date" id="payPeriodTo" name="payPeriodTo" required type="text">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="selectedDepartment">Department</label>
                        <div class="input-group">
                            <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                            <select class="form-control no-border-radius" id="selectedDepartment" name="selectedDepartment" required style="width: 100%;">
                                <option selected value="">
                                    --- SELECT FROM THE LIST ---
                                </option>
                                <optgroup label="Administrative Departments">
                                    @foreach( $adminDeptsList as $item )
                                        <option value="{{ $item->id }}">
                                            {{ $item->deptcode . ': ' . $item->deptname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Academic Departments">
                                    @foreach( $acadDeptsList as $item )
                                        <option value="{{ $item->id }}">
                                            {{ $item->deptcode . ': ' . $item->deptname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="excludedEmpNum">Employees to Exclude</label>
                        <div class="input-group">
                            <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                            <div class="textbox-has-button-container">
                                <select class="form-control no-border-radius" id="empNumExcluded" name="empNumExcluded" style="width: 84.5%;">
                                    <option selected value="">
                                        --- SELECT FROM THE LIST ---
                                    </option>
                                    <optgroup label="Employee List">
                                        @foreach( $employeeData as $modelObject )
                                            <option value="{{ $modelObject->employeeNumber }}">
                                                {{ $modelObject->employeeNumber . ': ' . $modelObject->fullName }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <select class="form-control no-border-radius" id="empExclReason" name="empExclReason" style="width: 84.5%;">
                                    <optgroup label="Employee List">
                                        @foreach( $exclusionsList as $modelObject )
                                            <option value="{{ $modelObject->id }}">
                                                {{ $modelObject->exclusion_reason }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <button class="btn btn-primary no-border-radius textbox-side-button" id="addEmpToList" type="button"><span class="fa fa-plus"></span> Add</button>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="empExcludedList">Reasons</label>
                        <div class="input-group">
                            <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-list-alt"></span></span>
                            <select class="form-control no-border-radius" id="empExcludedList" multiple name="empExcludedList[]" readonly size="6">
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Override Automatic Computation</label>
                        <div>
                            <label class="form-radio-inline override-bold-label">
                                <input checked class="form-radio-input" id="oacHide" name="overrideComputation" type="radio" value="no"> No
                            </label>
                            <label class="form-radio-inline override-bold-label">
                                <input class="form-radio-input" id="oacShow" name="overrideComputation" type="radio" value="yes"> Yes
                            </label>
                        </div>
                        <div id="checkBoxGroup">
                            <div class="alert no-border-radius" id="overrideWarning" role="alert">
                                <span class="fa fa-exclamation-triangle"></span>
                                <strong>Warning! </strong>
                                Automatic computation for the selected values will be overridden
                            </div>
                            <p class="help-block">
                                Select the fields to be computed manually
                            </p>
                            <label class="form-check-inline override-bold-label">
                                <input class="form-check-input" name="whTax" type="checkbox"> Withholding Tax
                            </label>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="box-footer">
                <button class="btn btn-warning no-border-radius" id="deleteSelected" type="button"><span class="fa fa-trash"></span> Delete Selected</button>
                <button class="btn btn-danger no-border-radius" id="clearList" type="button"><span class="fa fa-trash"></span> Clear List</button>
                <button class="btn btn-success no-border-radius pull-right" form="mainForm" type="submit"><span class="fa fa-file"></span> Generate Report</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script>
    // Global Scope Variables
    let regexPattern       = new RegExp( "^([a-zA-Z0-9]{8,16})+$" ),
        paymentDate        = $( '#paymentDate' ),
        earningPeriod      = $( '#earningPeriod' ),
        excludedList       = $( "#empExcludedList" ),
        checkBoxGroup      = $( '#checkBoxGroup' ),
        payPeriodFrom      = $( '#payPeriodFrom' ),
        payPeriodTo        = $( '#payPeriodTo' ),
        oacHide            = $( '#oacHide' ),
        oacShow            = $( '#oacShow' ),
        deleteSelected     = $( '#deleteSelected' ),
        clearList          = $( '#clearList' ),
        addEmpToList       = $( '#addEmpToList' ),
        reportType         = $( '#reportType' ),
        selectedDepartment = $( '#selectedDepartment' ),
        mainForm           = $( '#mainForm' ),
        empNumExcluded     = $( '#empNumExcluded' ),
        empExclReason      = $( '#empExclReason' );

    function addEmployeeNumberToList() {

        if( regexPattern.test( empNumExcluded.val() ) ) {
            if( checkForDuplicatesInEmployeeList() ) {
                excludedList.append(
                    $( "<option>", {
                        value: empNumExcluded.val().concat( empExclReason.val() ),
                        text: empNumExcluded.val() + ' - ' + empExclReason.select2('data')[0].text
                    })
                );
                empNumExcluded.val( null );
            } else {
                showDialog(
                    BootstrapDialog.TYPE_WARNING,
                    "Warning",
                    "Duplicate Employee number found in list."
                );
            }
        } else {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "Employee Number is either empty or invalid."
            );
        }
    }

    function checkForDuplicatesInEmployeeList() {
        let flag = true;
        excludedList.find( "option" ).each( function () {
            if( this.value === empNumExcluded.val() ) {
                flag = false;
            }
        });
        return flag;
    }

    function clearExcludedEmployeesList() {
        if( excludedList.find( "option" ).length == 0 ) {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "List is empty, nothing to delete."
            );
        } else {
            excludedList.empty();
        }
    }

    function deleteSelectedItems() {
        let counterFlag = 0;

        excludedList.find( "option" ).each( function () {
            if( this.selected ) {
                counterFlag++;
            }
        });

        if( excludedList.find( "option" ).length == 0 ) {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "List is empty, Nothing to delete."
            );
        } else if( excludedList.find( "option" ).length > 0 && counterFlag == 0 ) {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "Nothing selected, are you kidding me?"
            );
        } else {
            excludedList.find( "option" ).each( function () {
                if( this.selected ) {
                    this.remove();
                }
            });
        }
    }

    function resetCutOffDate() {
        payPeriodFrom.val( null );
        payPeriodTo.val( null )
        earningPeriod.val( null );
        earningPeriod.trigger( 'change' );
    }

    function selectAllItems() {
        excludedList.find( "option" ).prop( "selected", true );
    }

    function setCutOffDate() {
        if( paymentDate.val() ) {
            let from = new moment( paymentDate.val() ),
                to   = new moment( paymentDate.val() );

            if( earningPeriod.val() === '1' ) {
                payPeriodFrom.val( from.startOf( 'month' ).format( 'YYYY-MM-DD' ) );
                payPeriodTo.val( to.endOf( 'month' ).format( 'YYYY-MM-DD' ) );
            }
        } else {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "Please select the year & month first!"
            );

            earningPeriod.val( null );
        }
    }

    function showCheckBoxGroup( flag ) {
        if( flag ) {
            $( checkBoxGroup ).show();
            $( checkBoxGroup ).find( 'input' ).prop( 'disabled', false );
        } else {
            $( checkBoxGroup ).hide();
            $( checkBoxGroup ).find( 'input' ).prop( 'disabled', true );
        }
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

    $( document ).ready( function() {
        // Configure Addons
        paymentDate.datepicker({
            format: 'yyyy-mm',
            startView: 'months',
            minViewMode: 'months'
        });

        payPeriodFrom.datepicker({
            format: 'yyyy-mm-dd'
        });

        payPeriodTo.datepicker({
            format: 'yyyy-mm-dd'
        });

        reportType.select2();

        earningPeriod.select2();

        selectedDepartment.select2();

        empNumExcluded.select2();

        empExclReason.select2();

        // Register Event Handlers
        mainForm.submit( function() {
            selectAllItems()
        });

        paymentDate.change( function() {
            resetCutOffDate();
        });

        earningPeriod.change( function() {
            setCutOffDate();
        });

        addEmpToList.click( function() {
            addEmployeeNumberToList();
        });

        empNumExcluded.keypress( function( event ) {
            if( event.keyCode === 13 ) {
                addEmployeeNumberToList();
                event.preventDefault();
            }
        });

        oacHide.click( function() {
            showCheckBoxGroup( false );
        });

        oacShow.click( function() {
            showCheckBoxGroup( true );
        });

        deleteSelected.click( function() {
            deleteSelectedItems();
        });

        clearList.click( function() {
            clearExcludedEmployeesList();
        });

        // Trigger Events
        showCheckBoxGroup( false );
    });
</script>
@endsection
