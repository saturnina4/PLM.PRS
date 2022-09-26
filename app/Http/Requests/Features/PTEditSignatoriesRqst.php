<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Requests\Features;

use Illuminate\Foundation\Http\FormRequest;
use MiSAKACHi\VERACiTY\Http\Models\EmployeeDetailsMdl;

class PTEditSignatoriesRqst extends FormRequest
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
        $empDetailsModel  = new EmployeeDetailsMdl();

        return [
            'employee1' => "bail|required|string|size:8|exists:{$empDetailsModel->table},employeeNumber|different:employee2|different:employee3|different:employee4",
            'position1' => 'bail|required|string|max:50',
            'employee2' => "bail|required|string|size:8|exists:{$empDetailsModel->table},employeeNumber|different:employee1|different:employee3|different:employee4",
            'position2' => 'bail|required|string|max:50',
            'employee3' => "bail|required|string|size:8|exists:{$empDetailsModel->table},employeeNumber|different:employee2|different:employee1|different:employee4",
            'position3' => 'bail|required|string|max:50',
            'employee4' => "bail|required|string|size:8|exists:{$empDetailsModel->table},employeeNumber|different:employee2|different:employee3|different:employee1",
            'position4' => 'bail|required|string|max:50'
        ];
    }

    public function messages() {
        return [
            'employee1.*' => 'Invalid employee for the 1st signatory.',
            'position1.*' => 'Invalid position for the 1st signatory.',
            'employee2.*' => 'Invalid employee for the 2nd signatory.',
            'position2.*' => 'Invalid position for the 2nd signatory.',
            'employee3.*' => 'Invalid employee for the 3rd signatory.',
            'position3.*' => 'Invalid position for the 3rd signatory.',
            'employee4.*' => 'Invalid employee for the 4th signatory.',
            'position4.*' => 'Invalid position for the 4th signatory.'
        ];
    }
}
