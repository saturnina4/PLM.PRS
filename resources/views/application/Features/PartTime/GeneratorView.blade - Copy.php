@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Part-Time Payroll
@endsection

@section( 'additionalCss' )
<link rel="stylesheet" href="/files/css/GlobalAppStyles.css">
<link rel="stylesheet" href="/files/css/GPReportGenerator.css">
@endsection

@section( 'pageSectionTitle' )
<h1>
    Part-Time Payroll <small>Generate Report</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Part-Time Payroll</a>
    </li>
    <li>
        <a href="#">New Report</a>
    </li>
</ol>
@endsection


@section( 'pt-menu-active' )
active
@endsection

@section( 'pt-gen-active' )
active
@endsection

@section( 'pt-gen-regular-active' )
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
                <form action="" id="mainForm" method="post" name="mainForm">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">

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
                                        <th class="table-custom" style="text-align: center;">EXCLUDE</th>
                                        <th class="table-custom" style="text-align: center;">EMPLOYEE NAME</th>
                                        <th class="table-custom" style="text-align: center;">NO. OF HOURS</th>
                                        <th class="table-custom" style="text-align: center;">OTHER DEDUCTIONS</th>
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
                                        <th class="table-custom" style="text-align: center;">EMPLOYEE NAME</th>
                                        <th class="table-custom" style="text-align: center;">NO. OF HOURS</th>
                                        <th class="table-custom" style="text-align: center;">OTHER DEDUCTIONS</th>
                                        <th class="table-custom" style="text-align: center;">YEAR AND MONTH</th>
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
                <button class="btn btn-success no-border-radius pull-right" form="mainForm" type="submit"><span class="fa fa-file"></span> Generate Report</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section( 'additionalJs' )
<script>
    // Global Scope Variables
    let regexPattern       = new RegExp( "^([0-9]{1,16})+$" ),
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
        empIncluded        = $( '#empIncluded' ),
        empTable           = $( '#empTable' ),
        empIncludedTable   = $( '#empIncludedTable' );

    function guid() {
        function s4() {
            return Math.floor( ( 1 + Math.random() ) * 0x10000 )
            .toString( 16 )
            .substring( 1 );
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
    }

    function addEmployeeNumberToList() {
        if( regexPattern.test( empIncluded.val() ) ) {
            let t = empIncludedTable.DataTable();
            let uuid = guid();

            // add employee to the empIncluded table
            i = t.row.add( [
                '<button class="btn btn-danger" data-id="' + uuid + '" name="exclude" type="button"><span class="glyphicon glyphicon-remove"></span></button>',
                $( "#empIncluded option[value='" + empIncluded.val() + "']" ).text(),
                '<input class="form-control" data-id="' + empIncluded.val() + '" name="incNoOfHrs[]" pattern="^[1-9]([0-9])?(\\.[0-9]{1,2})?$"\
                    required style="text-align: right; width: 100%" type="text">',
                '<input class="form-control" data-id="' + empIncluded.val() + '" name="incOtherDeduc[]" pattern="^[1-9]([0-9]{1,5})?(\\.[0-9]{1,2})?$"\
                    style="text-align: right; width: 100%" type="text">',
                '<input class="form-control input-group date" data-id="' + empIncluded.val() + '" name="incYearMonth[]" required style="width: 100%;" type="text">\
                    <input name="empInc[]" type="hidden" value="' + empIncluded.val() + '">'
            ] ).draw( false ).index();

            // set the data-id for the new row
            t.rows( i ).nodes().to$().attr( 'data-id', uuid );

            // set the datepicker for the new row
            $( '.date[data-id="' + empIncluded.val() + '"]' ).datepicker({
                format: 'yyyy-mm',
                startView: 'months',
                minViewMode: 'months'
            }).on( 'change', function () {
                // check if the year and month is not equal to another row of the same employee
                if ( this.value != '' ) {
                    let empId = $( this ).data( 'id' );
                    let yearMonth = this.value;
                    let count = 0;

                    $( '.date[data-id="' + empId + '"]' ).each( function ( i, el ) {
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

            // set the click event of the button on the new row (deletion of row)
            $( 'button' ).on( 'click', function () {
                var row = $( 'tr[data-id="' + $( this ).data( 'id' ) + '"]' );
                var index = row.index();
                t.row( index ).remove().draw( false );
            } );

            // reset the selected option of empIncluded
            empIncluded.val( '' ).trigger( 'change' );
        } else {
            showDialog(
                BootstrapDialog.TYPE_WARNING,
                "Warning",
                "Employee Number is either empty or invalid."
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
            url      : '/parttime/report/fetch/employees'
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
                    // add employee to the dropdown
                    empIncluded.append( $( '<option></option>' )
                        .attr( 'value', val.id )
                        .text( val.fullName )
                    );

                    // add employee to the emp table
                    t1.row.add( [
                        '<input name="empId[]" type="hidden" value="' + val.id + '">\
                            <input data-id="' + val.id + '" name="exclude[]" type="checkbox">',
                        val.fullName,
                        '<input class="form-control" data-id="' + val.id + '" name="noOfHrs[]" pattern="^[1-9]([0-9]{1,5})?(\\.[0-9]{1,2})?$"\
                            required style="text-align: right; width: 100%" type="text">',
                        '<input class="form-control" data-id="' + val.id + '" name="otherDeduc[]" pattern="^[1-9]([0-9])?(\\.[0-9]{1,2})?$"\
                            style="text-align: right; width: 100%" type="text">'
                    ] ).draw( false );
                });

                // disabling of noOfHrs textbox if excluded (checked)
                $( 'input[name="exclude[]"]' ).on( 'click', function () {
                    let noOfHrs     = $( 'input[name="noOfHrs[]"][data-id="' + $( this ).data( 'id' ) + '"]' );
                    let otherDeduc  = $( 'input[name="otherDeduc[]"][data-id="' + $( this ).data( 'id' ) + '"]' );
                    let hiddenEmpId = $( 'input[name="empId[]"][value="' + $( this ).data( 'id' ) + '"]' );

                    if ( $( this ).is( ':checked' ) ) {
                        noOfHrs.prop( 'disabled', true );
                        otherDeduc.prop( 'disabled', true );
                        hiddenEmpId.prop( 'disabled', true );
                    } else {
                        noOfHrs.prop( 'disabled', false );
                        otherDeduc.prop( 'disabled', false );
                        hiddenEmpId.prop( 'disabled', false );
                    }
                } );
            } catch( errorException ) {
                alert(errorException);
            }
        }).fail( function( xhr, status, error ) {
            alert(error);
        });
    }

    $( document ).ready( function() {
        // Configure Addons
        paymentDate.datepicker( {
            format: 'yyyy-mm',
            startView: 'months',
            minViewMode: 'months'
        } );

        empTable.DataTable( {
            scrollX: true,
            autoWidth: true,
            ordering: false,
            aoColumns: [
                { 'sWidth': '10%' },
                { 'sWidth': '60%' },
                { 'sWidth': '15%' },
                { 'sWidth': '15%' }
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center"
            }],
        } );

        empIncludedTable.DataTable({
            scrollX: true,
            autoWidth: true,
            ordering: false,
            aoColumns: [
                { 'sWidth': '10%' },
                { 'sWidth': '35%' },
                { 'sWidth': '15%' },
                { 'sWidth': '15%' },
                { 'sWidth': '15%' }
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center"
            }],
        });

        selectedDepartment.select2();

        empIncluded.select2();

        // Register Event Handlers
        addEmpToList.click( function() {
            addEmployeeNumberToList();
        });

        empIncluded.keypress( function( event ) {
            if( event.keyCode === 13 ) {
                addEmployeeNumberToList();
                event.preventDefault();
            }
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
    });
</script>
@endsection
