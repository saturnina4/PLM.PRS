<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Requests\Features;

use Illuminate\Foundation\Http\FormRequest;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;

class CPGenerateRqst extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $departmentsModel = new DepartmentsMdl;
        $employeeDetailsModel = new EmployeeDetailsMdl;

        return [
            'paymentDate'  => 'bail|required|date_format:Y-m',
            'cutOffPeriod' => 'bail|required|in:1,2',
            'empId.*'      => "bail|required|numeric|exists:{$employeeDetailsModel->table},employeeNumber",
            'empId.*'      => "bail|nullable|numeric",
            'noOfDays'     => 'bail|nullable|array',
            'noOfDays.*'   => 'bail|nullable|numeric|min:1|max:12',
        ];
    }

    public function messages() {
        return [
            'cutOffPeriod.*' => 'Invalid cut-off period.',
            'paymentDate.*'  => 'Invalid pay period.',
            'empId.*'        => 'Employee not found.',
            'noOfDays.*'     => 'Invalid number of days in the employee list.',
        ];
    }
}
