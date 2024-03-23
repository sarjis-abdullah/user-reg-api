<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;

class DaliySummaryRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "branchId" => "numeric",
            "date" => "date_format:Y-m-d",
            "startDate" => "date_format:Y-m-d",
            "endDate" => "date_format:Y-m-d",
        ];
    }
}
