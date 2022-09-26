<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Requests\Features;

use Illuminate\Foundation\Http\FormRequest;

class PTViewSummaryRqst extends FormRequest
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
        return [
            'yearAndMonth' => 'bail|required|date_format:Y-m'
        ];
    }

    public function messages() {
        return [
            'yearAndMonth.*' => 'Invalid pay period.'
        ];
    }
}
