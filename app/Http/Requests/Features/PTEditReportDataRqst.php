<?php
declare( STRICT_TYPES = 1 );

namespace MiSAKACHi\VERACiTY\Http\Requests\Features;

use Illuminate\Foundation\Http\FormRequest;
use MiSAKACHi\VERACiTY\Http\Models\PTReportDataMdl;
use MiSAKACHi\VERACiTY\Http\Models\PTReportParamsMdl;

class PTEditReportDataRqst extends FormRequest
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
        $ptReportDataModel   = new PTReportDataMdl;
        $ptReportParamsModel = new PTReportParamsMdl;

        return [
            'recordId'        => "bail|required|string|exists:{$ptReportDataModel->table},unique_id",
            'reportId'        => "bail|required|string|exists:{$ptReportParamsModel->table},unique_id",
            'noOfHrs'         => 'bail|required|numeric|min:1|max:99',
            'taxPercent'     => 'bail|nullable|numeric|min:0|max:32',
            'otherDeductions' => 'bail|nullable|numeric|min:0|max:1073741825',
            'yearMonth'       => "bail|nullable|date_format:Y-m|before_or_equal:" .
                $ptReportParamsModel->find( $this->reportId )->earningYear . "-" .
                $ptReportParamsModel->find( $this->reportId )->earningMonth,
            'remarks'         => 'bail|nullable|string|max:20'
        ];
    }

    public function messages() {
        return [
            'recordId.*'        => 'Report data not found.',
            'reportId.*'        => 'Report not found.',
            'noOfHrs.*'         => 'Invalid number of hours.',
            'taxPercent.*'     => 'Invalid tax percentage.',
            'otherDeductions.*' => 'Invalid amount for other deductions.',
            'yearMonth.*'       => 'New pay period must be less than or equal to the current pay period.',
            'remarks.*'         => 'Invalid remarks.'
        ];
    }
}
