<?php

namespace MiSAKACHi\VERACiTY\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FindDeductionsRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'yearAndMonth'       => 'bail|required|date_format:Y-m',
            'earningPeriod'      => 'bail|required|digits_between:1,2'
        ];
    }

    public function messages() {
        return [
            'yearAndMonth.*'     => 'Invalid date format for the field Year & Month',
            'earningPeriod.*'    => 'Cut-Off Period must be between 1 & 2'
        ];
    }

}
