<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Controllers\Application\Features;

use Illuminate\Contracts\View\View;
use MiSAKACHi\VERACiTY\Http\Controllers\Controller;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;

final class ViewDeductionsListCtrl extends Controller {
    protected $deductionsModel,
              $employeeDetailsModel;

    public function __construct() {
        $this->deductionsModel      = new DeductionsMdl();
        $this->employeeDetailsModel = new EmployeeDetailsMdl();
    }

    public function getAction() : View {
        $employeeInformation = $this->deductionsModel
            ->select( "{$this->deductionsModel->table}.*", 'ed.employeeNumber', 'ed.fullName' )
            ->join( "{$this->employeeDetailsModel->table} AS ed", 'ed.employeeNumber', '=', "{$this->deductionsModel->table}.empNumber" )
            ->get();

        return view( 'Application.Features.Deductions.ViewDeductionsList' )
            ->with( 'employeeInformation', $employeeInformation );
    }
}
