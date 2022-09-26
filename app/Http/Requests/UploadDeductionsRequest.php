<?php

namespace MiSAKACHi\VERACiTY\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDeductionsRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'inputFile'   => 'bail|required|file'
        ];
    }

    public function messages() {
        return [
            'inputFile.*' => 'Invalid File Uploaded'
        ];
    }
}
