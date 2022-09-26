<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features\JobOrder;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;
use MiSAKACHi\VERACiTY\Http\Models\JoEmployeeDataMdl;

final class JoParametersListCtrl extends Controller {
    public function __construct() {
        // TODO
    }

    public function getAction() : View {
        $employeeDetailsModel = new EmployeeDetailsMdl();
        $joEmployeeDataModel  = new JoEmployeeDataMdl();

        $joEmployeeData = DB::table( "{$joEmployeeDataModel->table} AS jed" )
            ->select( "jed.*", 'ed.employeeNumber', 'ed.fullName' )
            ->join( "{$employeeDetailsModel->table} AS ed", 'ed.employeeNumber', "jed.employeeNumber" )
            ->get();

        return view( 'Application.Features.JobOrder.Parameters.ViewList' )
            ->with( 'employeeData',$joEmployeeData );
    }
}
