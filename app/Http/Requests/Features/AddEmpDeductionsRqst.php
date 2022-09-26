<?php

namespace MiSAKACHi\VERACiTY\Http\Requests\Features;

use Illuminate\Foundation\Http\FormRequest;
use MiSAKACHi\VERACiTY\Http\Models\DeductionsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;

final class AddEmpDeductionsRqst extends FormRequest {
    protected $deductionsModel,
              $employeeModel;

    public function authorize() {
        return true;
    }

    public function rules() {
        $this->employeeModel   = new EmployeeDetailsMdl();
        $this->deductionsModel = new DeductionsMdl();

        return [
            'employeeNumber'   => ['bail', 'required', 'string', "exists:{$this->employeeModel->table},employeeNumber", "unique:{$this->deductionsModel->table},empNumber"],
            'lvtPay'           => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisPolicy'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisEmergency'    => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisUmidCa'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisUoliLoan'     => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisUoliPolicy'   => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisEducation'    => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisConsolidated' => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisGfal'         => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisMpl'          => ['bail', 'required', 'numeric', 'max:1073741824'],
            'gsisComputerLoan' => ['bail', 'required', 'numeric', 'max:1073741824'],
            'landBank'         => ['bail', 'required', 'numeric', 'max:1073741824'],
            'plmPcci'          => ['bail', 'required', 'numeric', 'max:1073741824'],
            'philamLife'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'pagIbigPremium'   => ['bail', 'required', 'numeric', 'max:1073741824'],
            'pagIbigMpl'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'pagIbigEcl'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'pagIbigMp2'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'nhmfc'            => ['bail', 'required', 'numeric', 'max:1073741824'],
            'maxicare'         => ['bail', 'required', 'numeric', 'max:1073741824'],
            'studyGrant'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'otherBills'       => ['bail', 'required', 'numeric', 'max:1073741824'],
            'manualWhTax'      => ['bail', 'required', 'numeric', 'max:1073741824'],
            'atDays'           => ['bail', 'required', 'integer', 'max:256'],
            'atHours'          => ['bail', 'required', 'integer', 'max:256'],
            'atMinutes'        => ['bail', 'required', 'integer', 'max:256'],
            'mode'             => ['bail', 'required', 'string', 'in:insert']
        ];
    }
}
