<?php

namespace MiSAKACHi\VERACiTY\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetTrancheRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'activeTrancheVersion'   => 'bail|required|digits:1',
            'activeTranche'          => 'bail|required|digits:1|between:1,4'
        ];
    }

    public function messages() {
        return [
            'activeTrancheVersion.*' => 'Tranche Version received is invalid',
            'activeTranche.*'        => 'Tranche received is invalid'
        ];
    }
}
