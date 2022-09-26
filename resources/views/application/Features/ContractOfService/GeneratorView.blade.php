@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Contract of Servic Payroll
@endsection

@section( 'additionalCss' )
<link rel="stylesheet" href="/files/css/GlobalAppStyles.css">
<link rel="stylesheet" href="/files/css/GPReportGenerator.css">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Contract of Servic Payroll <small>Generate Report</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Contract of Servic Payroll</a>
    </li>
    <li>
        <a href="#">New Report</a>
    </li>
</ol>
@endsection


@section( 'cos-menu-active' )
active
@endsection

@section( 'cos-gen-active' )
active
@endsection

@section( 'cos-gen-regular-active' )
active
@endsection

@section( 'pageContent' )
<div class="row">
    <div class="col-md-11">
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
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <label for="selectedDepartment">College</label>

                        <div class="input-group">
                            <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-bars"></span></span>
                            <select class="form-control no-border-radius" id="selectedDepartment" name="selectedDepartment" required style="width: 100%;">
                                <option selected value="">
                                    --- SELECT FROM THE LIST ---
                                </option>

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
                        <label for="empTable">Employee List</label>

                        <div>
                            <table class="table table-bordered table-striped table-hover" id="empTable">
                                <thead class="thead-inverse">
                                    <tr>
                                        <!-- <th class="table-custom" style="text-align: center;">EXCLUDE</th> -->
                                        <th class="table-custom" style="text-align: center;">EMPLOYEE DATA</th>
                                        <th class="table-custom" style="text-align: center;">NO. OF HOURS</th>
                                        <th class="table-custom" style="text-align: center;">TAX %</th>
                                        <th class="table-custom" style="text-align: center;">OTHER DEDUCTIONS</th>
                                        <th class="table-custom" style="text-align: center;">REMARKS</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <label for="excludedEmpNum">Employees to Include From Previous Months</label>

                        <div class="input-group">
                            <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                            <div class="textbox-has-button-container">
                                <select class="form-control no-border-radius" disabled="" id="empIncluded" name="empIncluded" style="width: 84.5%;">
                                    <option selected value="">
                                        --- SELECT FROM THE LIST ---
                                    </option>
                                </select>

                                <button class="btn btn-primary no-border-radius textbox-side-button" disabled="" id="addEmpToList" type="button">
                                    <span class="fa fa-plus"></span> Add
                                </button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <label for="empIncludedTable">Included Employees From Previous Months</label>

                        <div>
                            <table class="table table-bordered table-striped table-hover" id="empIncludedTable">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th class="table-custom" style="text-align: center;">REMOVE</th>
                                        <th class="table-custom" style="text-align: center;">EMPLOYEE DATA</th>
                                        <th class="table-custom" style="text-align: center;">NO. OF HOURS</th>
                                        <th class="table-custom" style="text-align: center;">TAX<br>%</th>
                                        <th class="table-custom" style="text-align: center;">OTHER DEDUCTIONS</th>
                                        <th class="table-custom" style="text-align: center;">YEAR AND MONTH</th>
                                        <th class="table-custom" style="text-align: center;">REMARKS</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <label for="excludedEmpNum">Employees to Include From Other Colleges</label>

                        <div class="input-group">
                            <span class="input-group-addon no-border-radius" id="sizing-addon2"><span class="fa fa-user"></span></span>
                            <div class="textbox-has-button-container">
                                <select class="form-control no-border-radius" id="empOtherCollege" name="empOtherCollege" style="width: 84.5%;">
                                    <option selected value="">
                                        --- SELECT FROM THE LIST ---
                                    </option>
                                    @foreach ( $ptEmpList as $modelObject )
                                        <option value="{{ $modelObject['id'] }}">{{ $modelObject['fullName'] . ' - ' . $modelObject['positionName'] . ( $modelObject['academicType'] == 'G' ? ' - GP' : '' ) }}</option>
                                    @endforeach
                                </select>

                                <button class="btn btn-primary no-border-radius textbox-side-button" id="addEmpToList1" type="button">
                                    <span class="fa fa-plus"></span> Add
                                </button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group">
                        <label for="empOtherCollegeTable">Included Employees From Other Colleges</label>

                        <div>
                            <table class="table table-bordered table-striped table-hover" id="empOtherCollegeTable">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th class="table-custom" style="text-align: center;">REMOVE</th>
                                        <th class="table-custom" style="text-align: center;">EMPLOYEE DATA</th>
                                        <th class="table-custom" style="text-align: center;">NO. OF HOURS</th>
                                        <th class="table-custom" style="text-align: center;">TAX<br>%</th>
                                        <th class="table-custom" style="text-align: center;">OTHER DEDUCTIONS</th>
                                        <th class="table-custom" style="text-align: center;">YEAR AND MONTH</th>
                                        <th class="table-custom" style="text-align: center;">REMARKS</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
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
    let regexPattern         = new RegExp( "^([0-9]{1,16})+$" ),
        paymentDate          = $( '#paymentDate' ),
        earningPeriod        = $( '#earningPeriod' ),
        addEmpToList         = $( '#addEmpToList' ),
        addEmpToList1        = $( '#addEmpToList1' ),
        selectedDepartment   = $( '#selectedDepartment' ),
        mainForm             = $( '#mainForm' ),
        submitBtn            = $( '#submitBtn' ),
        validationErrors     = $( '#validationErrors' ),
        empIncluded          = $( '#empIncluded' ),
        empOtherCollege      = $( '#empOtherCollege' ),
        empTable             = $( '#empTable' ),
        empOtherCollegeTable = $( '#empOtherCollegeTable' ),
        empIncludedTable     = $( '#empIncludedTable' );

    function guid() {
        function s4() {
            return Math.floor( ( 1 + Math.random() ) * 0x10000 )
            .toString( 16 )
            .substring( 1 );
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
    }

    function addEmployeeToList() {
        if( regexPattern.test( empIncluded.val() ) ) {
            let t = empIncludedTable.DataTable();
            let uuid = guid();

            // add employee to the empIncluded table
            i = t.row.add( [
                '<button class="btn btn-danger btnRemove" data-id="' + uuid + '" name="exclude" type="button"><span class="glyphicon glyphicon-remove"></span></button>',
                $( "#empIncluded option[value='" + empIncluded.val() + "']" ).text(),
                '<input class="form-control" data-id="' + empIncluded.val() + '" name="incNoOfHrs[]" required style="text-align: right; width: 100%" type="text">',
                '<input class="form-control" data-id="' + empIncluded.val() + '" name="incTaxPercent[]" required style="text-align: right; width: 100%" type="text">',
                '<input class="form-control" data-id="' + empIncluded.val() + '" name="incOtherDeduc[]" style="text-align: right; width: 100%" type="text">',
                '<input class="form-control input-group date" data-id="' + empIncluded.val() + '" name="incYearMonth[]" required style="width: 100%;" type="text">',
                '<input class="form-control" data-id="' + empIncluded.val() + '" name="incRemarks[]" required style="width: 100%;" type="text">\
                    <input name="empInc[]" type="hidden" value="' + empIncluded.val() + '">'
            ] ).draw( true ).index();

            // set the data-id for the new row
            t.rows( i ).nodes().to$().attr( 'data-id', uuid );
        } else {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "Employee is either empty or invalid."
            );
        }
    }

    function addOtherEmpToList() {
        if( regexPattern.test( empOtherCollege.val() ) ) {
            let t = empOtherCollegeTable.DataTable();
            let uuid = guid();

            // add employee to the empIncluded table
            i = t.row.add( [
                '<button class="btn btn-danger btnOtherRemove" data-id="' + uuid + '" name="exclude" type="button"><span class="glyphicon glyphicon-remove"></span></button>',
                $( "#empOtherCollege option[value='" + empOtherCollege.val() + "']" ).text(),
                '<input class="form-control" data-id="' + empOtherCollege.val() + '" name="otherNoOfHrs[]" required style="text-align: right; width: 100%" type="text">',
                '<input class="form-control" data-id="' + empOtherCollege.val() + '" name="otherTaxPercent[]" required style="text-align: right; width: 100%" type="text">',
                '<input class="form-control" data-id="' + empOtherCollege.val() + '" name="otherOtherDeduc[]" style="text-align: right; width: 100%" type="text">',
                '<input class="form-control input-group date" data-id="' + empOtherCollege.val() + '" name="otherYearMonth[]" required style="width: 100%;" type="text">',
                '<input class="form-control" data-id="' + empOtherCollege.val() + '" name="otherRemarks[]" required style="width: 100%;" type="text">\
                    <input name="empOther[]" type="hidden" value="' + empOtherCollege.val() + '">'
            ] ).draw( true ).index();

            // set the data-id for the new row
            t.rows( i ).nodes().to$().attr( 'data-id', uuid );
        } else {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "Employee is either empty or invalid."
            );
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

    function fetchEmployees( department ) {
        mainForm.LoadingOverlay( 'show' );
        $.ajax({
            async    : true,
            data     : {
                'deptId' : department
            },
            dataType : 'json',
            encode   : true,
            headers  : {
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            },
            type     : 'post',
            url      : '{{ config('app.root') }}/parttime/report/fetch/employees'
        }).done( function( data, status, xhr ) {
            try {
                let empData = data.empData;

                // reset the selected option
                empIncluded.find( 'option' )
                    .remove()
                    .end()
                    .append( '<option value="">--- SELECT FROM THE LIST ---</option>' )
                    .val( '' );

                // remove contents of emp and included table
                var t1 = empTable.DataTable();
                t1.clear().draw();

                var t2 = empIncludedTable.DataTable();
                t2.clear().draw();

                $.each( empData, function ( i, val ) {
                    var txtName = val.fullName + ' - ' + val.positionName + ( val.academicType == 'G' ? ' - GP' : '' );

                    // add employee to the dropdown
                    empIncluded.append( $( '<option></option>' )
                        .attr( 'value', val.id )
                        .text( txtName )
                    );

                    // add employee to the emp table
                    t1.row.add( [
                        // '<input name="empId[]" type="hidden" value="' + val.id + '">\
                        //     <input data-id="' + val.id + '" name="exclude[]" type="checkbox">\
                        //     <small class="text-red" id="employeeNumberErrorText"></small>',
                        txtName,
                        '<input name="empId[]" type="hidden" value="' + val.id + '">\
                            <input class="form-control" data-id="' + val.id + '" name="noOfHrs[]" required style="text-align: right; width: 100%" type="text">',
                        '<input class="form-control" data-id="' + val.id + '" name="taxPercent[]" required style="text-align: right; width: 100%" type="text">',
                        '<input class="form-control" data-id="' + val.id + '" name="otherDeduc[]" style="text-align: right; width: 100%" type="text">',
                        '<input class="form-control" data-id="' + val.id + '" name="remarks[]" style="width: 100%" type="text">'
                    ] ).draw( false );
                });
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

        mainForm.LoadingOverlay( 'hide' );
    }

    $( document ).ready( function() {
        // Configure Addons
        paymentDate.datepicker( {
            format     : 'yyyy-mm',
            startView  : 'months',
            minViewMode: 'months'
        } );

        empTable.DataTable( {
            scrollX: true,
            autoWidth: true,
            ordering: false,
            aoColumns: [
                // { 'sWidth': '10%' },
                { 'sWidth': '55%' },
                { 'sWidth': '10%' },
                { 'sWidth': '10%' },
                { 'sWidth': '10%' },
                { 'sWidth': '15%' }
            ],
            columnDefs: [{
                // "targets": 0,
                // "className": "text-center"
            }],
            drawCallback: function ( settings ) {
                // disabling of inputs if excluded (checked)
                $( 'input[name="exclude[]"]' ).on( 'click', function () {
                    let noOfHrs     = $( 'input[name="noOfHrs[]"][data-id="' + $( this ).data( 'id' ) + '"]' );
                    let otherDeduc  = $( 'input[name="otherDeduc[]"][data-id="' + $( this ).data( 'id' ) + '"]' );
                    let remarks     = $( 'input[name="remarks[]"][data-id="' + $( this ).data( 'id' ) + '"]' );
                    let taxPercent  = $( 'input[name="taxPercent[]"][data-id="' + $( this ).data( 'id' ) + '"]' );
                    let hiddenEmpId = $( 'input[name="empId[]"][value="' + $( this ).data( 'id' ) + '"]' );

                    if ( $( this ).is( ':checked' ) ) {
                        noOfHrs.prop( 'disabled', true );
                        otherDeduc.prop( 'disabled', true );
                        hiddenEmpId.prop( 'disabled', true );
                        remarks.prop( 'disabled', true );
                        taxPercent.prop( 'disabled', true );
                    } else {
                        noOfHrs.prop( 'disabled', false );
                        otherDeduc.prop( 'disabled', false );
                        hiddenEmpId.prop( 'disabled', false );
                        remarks.prop( 'disabled', false );
                        taxPercent.prop( 'disabled', false );
                    }
                } );
            }
        } );

        empIncludedTable.DataTable({
            scrollX: true,
            autoWidth: true,
            ordering: false,
            aoColumns: [
                { 'sWidth': '5%' },
                { 'sWidth': '39%' },
                { 'sWidth': '7%' },
                { 'sWidth': '7%' },
                { 'sWidth': '8%' },
                { 'sWidth': '10%' },
                { 'sWidth': '14%' }
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center"
            }],
            drawCallback: function ( settings ) {
              try {
                // set the datepicker for the new row
                $( '.date' ).datepicker({
                    format: 'yyyy-mm',
                    startView: 'months',
                    minViewMode: 'months'
                }).on( 'change', function () {
                    // check if the year and month is not equal to another row of the same employee
                    if ( this.value != '' ) {
                        let empId = $( this ).data( 'id' );
                        let yearMonth = this.value;
                        let count = 0;

                        empIncludedTable.DataTable().$( '.date[data-id="' + empId + '"]' ).each( function ( i, el ) {
                            if ( $( el ).datepicker().val() == yearMonth ) {
                                ++count;
                            }

                            // if yes, tell the user and set the date to empty
                            if ( count > 1 ) {
                                $( el ).datepicker( 'setDate', null );
                                showDialog(
                                    BootstrapDialog.TYPE_WARNING,
                                    "Warning",
                                    "Duplicate payment period."
                                );
                                return false;
                            }
                        } );
                    }
                });
              } catch ( errorException ) {
                alert(errorException);
              }
            }
        });
        
        // remove a row from included table
        $( '#empIncludedTable tbody' ).on( 'click', '.btnRemove', function () {
            empIncludedTable.DataTable().row( $( this ).parents( 'tr' ) ).remove().draw( false );
        } );

        empOtherCollegeTable.DataTable({
            scrollX: true,
            autoWidth: true,
            ordering: false,
            aoColumns: [
                { 'sWidth': '5%' },
                { 'sWidth': '39%' },
                { 'sWidth': '7%' },
                { 'sWidth': '7%' },
                { 'sWidth': '10%' },
                { 'sWidth': '10%' },
                { 'sWidth': '12%' }
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center"
            }],
            drawCallback: function ( settings ) {
              try {
                // set the datepicker for the new row
                $( '.date' ).datepicker({
                    format: 'yyyy-mm',
                    startView: 'months',
                    minViewMode: 'months'
                }).on( 'change', function () {
                    // check if the year and month is not equal to another row of the same employee
                    if ( this.value != '' ) {
                        let empId = $( this ).data( 'id' );
                        let yearMonth = this.value;
                        let count = 0;

                        empOtherCollegeTable.DataTable().$( '.date[data-id="' + empId + '"]' ).each( function ( i, el ) {
                            if ( $( el ).datepicker().val() == yearMonth ) {
                                ++count;
                            }

                            // if yes, tell the user and set the date to empty
                            if ( count > 1 ) {
                                $( el ).datepicker( 'setDate', null );
                                showDialog(
                                    BootstrapDialog.TYPE_WARNING,
                                    "Warning",
                                    "Duplicate payment period."
                                );
                                return false;
                            }
                        } );
                    }
                });
              } catch ( errorException ) {
                alert(errorException);
              }
            }
        });
        
        // remove a row from other college table
        $( '#empOtherCollegeTable tbody' ).on( 'click', '.btnRemove', function () {
            empOtherCollegeTable.DataTable().row( $( this ).parents( 'tr' ) ).remove().draw( false );
        } );

        selectedDepartment.select2();

        empIncluded.select2();

        empOtherCollege.select2();

        // Register Event Handlers
        addEmpToList.click( function() {
            addEmployeeToList();
        });

        addEmpToList1.click( function() {
            addOtherEmpToList();
        });

        selectedDepartment.on( 'change', function () {
            let department = $( this ).val();

            if ( department == '' ) {
                empIncluded.prop( 'disabled', true );
                addEmpToList.prop( 'disabled', true );
                var t1 = empIncludedTable.DataTable();
                t1.clear().draw();
                var t2 = empIncludedTable.DataTable();
                t2.clear().draw();
            } else {
                empIncluded.prop( 'disabled', false );
                addEmpToList.prop( 'disabled', false );
                fetchEmployees( department );
            }
        });

        submitBtn.on( 'click', function () {
            var table1 = empTable.DataTable();
            var table2 = empIncludedTable.DataTable();
            var table3 = empOtherCollegeTable.DataTable();

            let formData = paymentDate.serialize() + '&' +
                           selectedDepartment.serialize() + '&' +
                           table1.$( 'input' ).serialize() + '&' +
                           table2.$( 'input' ).serialize() + '&' +
                           table3.$( 'input' ).serialize();

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
                        window.location = '{{ config('app.root') }}/parttime/report/view/id/' + jsonData.recordId;
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
