<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Requests\Features;

use Illuminate\Foundation\Http\FormRequest;
use MiSAKACHi\VERACiTY\Http\Models\DepartmentsMdl;
use MiSAKACHi\VERACiTY\Http\Models\PartTimeEmpDetailsMdl;

class PTGenerateRqst extends FormRequest
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
        $partTimeEmpDetailsModel = new PartTimeEmpDetailsMdl;

        return [
            'selectedDepartment' => "bail|required|numeric|exists:{$departmentsModel->table},id",
            'paymentDate'        => 'bail|required|date_format:Y-m',
            'empId'              => 'bail|nullable|array',
            'empId.*'            => "bail|required|numeric|exists:{$partTimeEmpDetailsModel->table},id",
            'noOfHrs'            => 'bail|nullable|array',
            'noOfHrs.*'          => 'bail|nullable|numeric|min:1|max:99',//formerly required
            'taxPercent'        => 'bail|nullable|array',
            'taxPercent.*'      => 'bail|nullable|numeric|min:0|max:32',
            'otherDeduc'         => 'bail|nullable|array',
            'otherDeduc.*'       => 'bail|nullable|numeric|min:0|max:1073741825',
            'remarks'            => 'bail|nullable|array',
            'remarks.*'          => 'bail|nullable|string|max:20',
            'empInc'             => 'bail|nullable|array',
            'empInc.*'           => "bail|required|numeric|exists:{$partTimeEmpDetailsModel->table},id",
            'incNoOfHrs'         => 'bail|nullable|array',
            'incNoOfHrs.*'       => 'bail|required|numeric|min:1|max:99',
            'incTaxPercent'     => 'bail|nullable|array',
            'incTaxPercent.*'   => 'bail|nullable|numeric|min:0|max:32',
            'incOtherDeduc'      => 'bail|nullable|array',
            'incOtherDeduc.*'    => 'bail|nullable|numeric|min:0|max:1073741825',
            'incYearMonth'       => 'bail|nullable|array',
            'incYearMonth.*'     => "bail|required|date_format:Y-m|before:paymentDate",
            'incRemarks'         => 'bail|nullable|array',
            'incRemarks.*'       => 'bail|nullable|string|max:20',
            'empOther'           => 'bail|nullable|array',
            'empOther.*'         => "bail|required|numeric|exists:{$partTimeEmpDetailsModel->table},id",
            'otherNoOfHrs'       => 'bail|nullable|array',
            'otherNoOfHrs.*'     => 'bail|required|numeric|min:1|max:99',
            'otherTaxPercent'   => 'bail|nullable|array',
            'otherTaxPercent.*' => 'bail|nullable|numeric|min:0|max:32',
            'otherOtherDeduc'    => 'bail|nullable|array',
            'otherOtherDeduc.*'  => 'bail|nullable|numeric|min:0|max:1073741825',
            'otherYearMonth'     => 'bail|nullable|array',
            'otherYearMonth.*'   => "bail|nullable|date_format:Y-m|before_or_equal:paymentDate",
            'otherRemarks'       => 'bail|nullable|array',
            'otherRemarks.*'     => 'bail|nullable|string|max:20'
        ];
    }

    public function messages() {
        return [
            'selectedDepartment.*' => 'College not found.',
            'paymentDate.*'        => 'Invalid pay period.',
            'empId.*'              => 'Employee not found.',
            'noOfHrs.*'            => 'Invalid number of hours in the employee list.',
            'taxPercent.*'        => 'Invalid tax percentage in the employee list.',
            'otherDeduc.*'         => 'Invalid amount for other deductions in the employee list.',
            'remarks.*'            => 'Invalid remarks in the employee list.',
            'empInc.*'             => 'Employee not found.',
            'incNoOfHrs.*'         => 'Invalid number of hours in the table for included employees from previous months.',
            'incTaxPercent.*'     => 'Invalid tax percentage in the table for included employees from previous months.',
            'incOtherDeduc.*'      => 'Invalid amount for other deductions in the table for included employees from previous months.',
            'incYearMonth.*'       => 'The pay period for included employees from previous months must be less than the specified pay period.',
            'incRemarks.*'         => 'Invalid remarks in the table for included employees from previous months.',
            'empOther.*'           => 'Employee not found.',
            'otherNoOfHrs.*'       => 'Invalid number of hours in the table for included employees from other colleges.',
            'otherTaxPercent.*'   => 'Invalid tax percentage in the table for included employees from other colleges.',
            'otherOtherDeduc.*'    => 'Invalid amount for other deductions in the table for included employees from other colleges.',
            'otherYearMonth.*'     => 'The pay period for included employees from other colleges must be less than or equal to the specified pay period.',
            'otherRemarks.*'       => 'Invalid remarks in the table for included employees from other colleges.'
        ];
    }
}
