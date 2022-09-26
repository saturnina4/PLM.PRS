<?php

namespace MiSAKACHi\VERACiTY\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;

class GPRegReportFinderRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        $departmentsModel = new DepartmentsMdl;
        return [
            'selectedDepartment'   => "bail|required|numeric|exists:{$departmentsModel->table},id",
            'yearAndMonth'         => 'bail|required|date_format:Y-m',
            'earningPeriod'        => 'bail|required|digits_between:1,2'
        ];
    }

    public function messages() {
        return [
            'selectedDepartment.*' => 'Department not found',
            'yearAndMonth.*'       => 'Invalid date format for the field Year & Month',
            'earningPeriod.*'      => 'Cut-Off Period must be between 1 & 2'
        ];
    }

}
