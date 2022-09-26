@extends( 'layouts.navMain' )

@section( 'pageTitle' )
Part-Time Payroll Report
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
    Part-Time Payroll <small>Payroll Reports Viewer</small>
</h1>
@endsection

@section( 'breadCrumb' )
<ol class="breadcrumb">
    <li>
        <a href="#"><i class="fa fa-money"></i>Part-Time Payroll</a>
    </li>
    <li>
        <a href="#">View Reports</a>
    </li>
</ol>
@endsection

@section( 'pt-menu-active' )
active
@endsection

@section( 'pt-view-active' )
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
                            <td style="width: 120px;"><b>DEPARTMENT</b></td>
                            <td>{{ $departmentName }}</td>
                        </tr>
                        <tr>
                            <td style="width: 120px;"><b>PAY PERIOD</b></td>
                            <td>{{ $earningPeriod->format('F, Y') }}</td>
                        </tr>
                    </table>
                    <br>
                </div>

                <table class="table table-bordered table-striped table-hover" id="reportTable">
                    <thead class="thead-inverse">
                        <tr>
                            <th class="">ACTIONS</th>
                            <th class="table-custom text-center">EMPLOYEE NAME</th>
                            <th class="table-custom text-center">POSITION</th>
                            <th class="table-custom text-center">RATE PER HOUR</th>
                            <th class="table-custom text-center">NO. OF HOURS</th>
                            <th class="table-custom text-center">EARNED FOR THE PERIOD</th>
                            <th class="table-custom text-center">EWT</th>
                            <th class="table-custom text-center">W/TAX</th>
                            <th class="table-custom text-center">OTHER DEDUCTIONS</th>
                            <th class="table-custom text-center">NET AMOUNT</th>
                            <th class="table-custom text-center">REMARKS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach( $reportData as $modelObject )
                            <tr>
                                <td class="text-center text-nowrap action-column-2">
                                    <button class="btn btn-flat btn-danger btnExclude" data-id="{{ $modelObject['unique_id'] }}" type="button"><span class="fa fa-remove"></span></button>
                                    <button class="btn btn-flat btn-primary btnEdit" data-id="{{ $modelObject['unique_id'] }}" type="button"><span class="fa fa-pencil"></span></button>
                                </td>
                                <td>{{ $modelObject['empName'] }}</td>
                                <td>{{ $modelObject{'empDesignation'} }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empHourlyRate'],  2 ) }}</td>
                                <td class="table-data-right-align">{{ $modelObject['empNoOfHrs'] }}</td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empEarnedAmount'], 2 ) }}</td>
                                <td class="table-data-right-align">
                                    {{ number_format( $modelObject['tax_ewt'], 2 ) == 0 ? '' : number_format( $modelObject['tax_ewt'], 2 ) }}
                                </td>
                                <td class="table-data-right-align">
                                    {{ number_format( $modelObject['tax_whTax'], 2 ) == 0 ? '' : number_format( $modelObject['tax_whTax'], 2 ) }}
                                </td>
                                <td class="table-data-right-align">
                                    {{ number_format( $modelObject['otherDeductions'], 2 ) == 0 ? '' : number_format( $modelObject['otherDeductions'], 2 ) }}
                                </td>
                                <td class="table-data-right-align">{{ number_format( $modelObject['empNetAmount'], 2 ) }}</td>
                                <td>
                                    {{
                                        ltrim( $modelObject['yearMonth'] == $earningPeriod->format('Y-m') ?
                                        ( $modelObject['empAcademicType'] == 'G' ? 'GP' : '' ) . ( $modelObject['remarks'] != '' ? ' - ' . $modelObject['remarks'] : '' ) :
                                        \Carbon\Carbon::createFromDate(
                                            substr($modelObject['yearMonth'], 0, 4), substr($modelObject['yearMonth'], 5, 2), 1
                                        )->format('F, Y') . ( $modelObject['empAcademicType'] == 'G' ? ' - GP' : '' ) .
                                        ( $modelObject['remarks'] != '' ? ' - ' . $modelObject['remarks'] : '' ), ' - ' )
                                    }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">
                                Grand Total&nbsp;&nbsp;&nbsp;&nbsp;- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                            </th>
                            <th class="table-data-right-align"></th>
                            <th class="table-data-right-align"></th>
                            <th class="table-data-right-align"></th>
                            <th class="table-data-right-align"></th>
                            <th class="table-data-right-align"></th>
                            <th class="table-data-right-align"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="box-footer">
                <a class="btn btn-success no-border-radius" href="{{ config('app.root') }}/parttime/report/generate"><span class="fa fa-plus"></span> New Report</a>
                <a class="btn btn-info no-border-radius" href="{{ config('app.root') }}/parttime/report/find"><span class="fa fa-search"></span> Find Report</a>
                <button class="btn btn-danger no-border-radius" id="btnDeleteReport"><span class="fa fa-times"></span> Delete Report</button>
                <button class="btn btn-primary no-border-radius" id="btnAddEmployee"><span class="fa fa-plus"></span> Add New Employee</button>
                <a class="btn btn-default no-border-radius" href="{{ config('app.root') }}/parttime/report/download/id/{{ $recordId }}" id="downloadButton"><span class="fa fa-cloud-download"></span> Download Printable Format</a>
            </div>
        </div>
    </div>
</div>

<div id="editDataFormContainer" role="form">
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="editDataModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        <i class ="fa fa-pencil"></i> <span>Edit Employee Data</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="hidden" id="validationEditErrorCallout">
                        <div class="callout callout-danger flat">
                            <h4>
                                <i class="fa fa-warning"></i> Field Validation Error
                            </h4>
                            <p>
                                Please check the highlighted fields below.
                            </p>
                        </div>
                    </div>
                    <div class="hidden" id="errorEditMessageCallout">
                        <div class="callout callout-danger flat">
                            <h4>
                                <i class="fa fa-warning"></i> Application Error
                            </h4>
                            <p></p>
                        </div>
                    </div>
                    <form id="editDataForm" name="editDataForm">
                        <input id="recordId" name="recordId" type="hidden">
                        <input id="reportId" name="reportId" type="hidden" value="{{ $recordId }}">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> No. of Hours</small>
                                    <div class="input-group" id="noOfHrsContainerEdit">
                                        <span class="input-group-addon"><span class="fa fa-hourglass"></span></span>
                                        <label for="noOfHrsEdit" class="sr-only">No. of Hours</label>
                                        <input class="form-control input-group" id="noOfHrsEdit" name="noOfHrs" type="text">
                                    </div>
                                    <div id="noOfHrsErrorMsgEdit" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Tax Percentage</small>
                                    <div class="input-group" id="taxPercentContainerEdit">
                                        <span class="input-group-addon"><span class="fa fa-percent"></span></span>
                                        <label for="taxPercentEdit" class="sr-only">Tax Percentage</label>
                                        <input class="form-control input-group" id="taxPercentEdit" name="taxPercent" type="text">
                                    </div>
                                    <div id="taxPercentErrorMsgEdit" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Other Deductions</small>
                                    <div class="input-group" id="otherDeductionsContainerEdit">
                                        <span class="input-group-addon"><span class="fa fa-money"></span></span>
                                        <label for="otherDeductionsEdit" class="sr-only">Other Deductions</label>
                                        <input class="form-control input-group" id="otherDeductionsEdit" name="otherDeductions" type="text">
                                    </div>
                                    <div id="otherDeductionsErrorMsgEdit" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Year and Month</small>
                                    <div class="input-group" id="yearMonthContainerEdit">
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        <label for="yearMonthEdit" class="sr-only">Year and Month</label>
                                        <input class="form-control input-group date" id="yearMonthEdit" name="yearMonth" type="text">
                                    </div>
                                    <div id="yearMonthErrorMsgEdit" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Remarks</small>
                                    <div class="input-group" id="remarksContainerEdit">
                                        <span class="input-group-addon"><span class="fa fa-money"></span></span>
                                        <label for="remarksEdit" class="sr-only">Remarks</label>
                                        <input class="form-control input-group" id="remarksEdit" name="remarks" type="text">
                                    </div>
                                    <div id="remarksErrorMsgEdit" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button aria-label="Reset" class="btn btn-danger flat pull-left" id="btnEditReset" type="button"><i class="fa fa-eraser"></i> Reset Fields</button>
                    <button aria-label="Submit" class="btn btn-success flat pull-right" id="btnEditSubmit" type="button"><i class="fa fa-save"></i> Save Record</button>
                    <button aria-label="Close" class="btn btn-default flat pull-right" data-dismiss="modal" type="button"><i class="fa fa-times"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addDataFormContainer" role="form">
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="addDataModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        <i class ="fa fa-plus"></i> <span>Add Employee Data</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="hidden" id="validationAddErrorCallout">
                        <div class="callout callout-danger flat">
                            <h4>
                                <i class="fa fa-warning"></i> Field Validation Error
                            </h4>
                            <p>
                                Please check the highlighted fields below.
                            </p>
                        </div>
                    </div>
                    <div class="hidden" id="errorAddMessageCallout">
                        <div class="callout callout-danger flat">
                            <h4>
                                <i class="fa fa-warning"></i> Application Error
                            </h4>
                            <p></p>
                        </div>
                    </div>
                    <form id="addDataForm" name="addDataForm">
                        <input id="reportId" name="reportId" type="hidden" value="{{ $recordId }}">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-9">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Employee</small>
                                    <div class="input-group" id="empIdContainerAdd">
                                        <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                        <label for="empIdAdd" class="sr-only">empId</label>
                                        <select class="form-control input-group no-border-radius" id="empIdAdd" name="empId" style="width: 100%">
                                            <option selected value="">
                                                --- SELECT FROM THE LIST ---
                                            </option>
                                            @foreach ( $empData as $modelObject )
                                                <option value="{{ $modelObject['id'] }}">{{ $modelObject['fullName'] . ' - ' . $modelObject['positionName'] . ( $modelObject['academicType'] == 'G' ? ' - GP' : '' ) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="empIdErrorMsgAdd" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> No. of Hours</small>
                                    <div class="input-group" id="noOfHrsContainerAdd">
                                        <span class="input-group-addon"><span class="fa fa-hourglass"></span></span>
                                        <label for="noOfHrsAdd" class="sr-only">No. of Hours</label>
                                        <input class="form-control input-group" id="noOfHrsAdd" name="noOfHrs" type="text">
                                    </div>
                                    <div id="noOfHrsErrorMsgAdd" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Tax Percentage</small>
                                    <div class="input-group" id="taxPercentContainerAdd">
                                        <span class="input-group-addon"><span class="fa fa-percent"></span></span>
                                        <label for="taxPercentAdd" class="sr-only">Tax Percentage</label>
                                        <input class="form-control input-group" id="taxPercentAdd" name="taxPercent" type="text">
                                    </div>
                                    <div id="taxPercentErrorMsgAdd" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Other Deductions</small>
                                    <div class="input-group" id="otherDeductionsContainerAdd">
                                        <span class="input-group-addon"><span class="fa fa-money"></span></span>
                                        <label for="otherDeductionsAdd" class="sr-only">Other Deductions</label>
                                        <input class="form-control input-group" id="otherDeductionsAdd" name="otherDeductions" type="text">
                                    </div>
                                    <div id="otherDeductionsErrorMsgAdd" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Year and Month</small>
                                    <div class="input-group" id="yearMonthContainerAdd">
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        <label for="yearMonthAdd" class="sr-only">Year and Month</label>
                                        <input class="form-control input-group date" id="yearMonthAdd" name="yearMonth" type="text">
                                    </div>
                                    <div id="yearMonthErrorMsgAdd" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted"><i class="fa fa-asterisk text-green"></i> Remarks</small>
                                    <div class="input-group" id="remarksContainerAdd">
                                        <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                        <label for="remarksAdd" class="sr-only">Remarks</label>
                                        <input class="form-control input-group" id="remarksAdd" name="remarks" type="text">
                                    </div>
                                    <div id="remarksErrorMsgAdd" class="hidden">
                                        <i class="fa fa-warning text-danger"></i>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button aria-label="Reset" class="btn btn-danger flat pull-left" id="btnAddReset" type="button"><i class="fa fa-eraser"></i> Reset Fields</button>
                    <button aria-label="Submit" class="btn btn-success flat pull-right" id="btnAddSubmit" type="button"><i class="fa fa-save"></i> Save Record</button>
                    <button aria-label="Close" class="btn btn-default flat pull-right" data-dismiss="modal" type="button"><i class="fa fa-times"></i> Cancel</button>
                </div>
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
            order   : [[ 1, 'asc' ]],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "orderable": false
            }],
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;

                for ( var j = 5; j <= 9; ++j ) {
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // Total over all pages
                    total = api
                        .column( j )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column( j ).footer() ).html(
                        number_format( total.toFixed(2 ).toLocaleString() )
                    );
                }
            },
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
                window.location = '{{ route( 'getPTFindReport' ) }}';
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
