<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Requests\Features;

use Illuminate\Foundation\Http\FormRequest;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;

final class GPGenerateRqst extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        $departmentsModel = new DepartmentsMdl;

        return [
            'reportType'          => 'bail|required|numeric|min:0|max:15',
            'selectedDepartment'  => "bail|required|numeric|exists:{$departmentsModel->table},id",
            'empExcludedList'     => 'bail|array',
            'empExcludedList.*'   => 'bail|digits_between:9,12',
            'paymentDate'         => 'bail|required|date',
            'earningPeriod'       => 'bail|required|digits_between:1,2',
            'payPeriodFrom'       => 'bail|required|date',
            'payPeriodTo'         => 'bail|required|date',
            'overrideComputation' => 'bail|required|string|in:yes,no',
            'whTax'               => 'bail|string|nullable|in:on'
        ];
    }
}
