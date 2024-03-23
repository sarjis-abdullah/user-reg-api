<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class DateWiseProfitRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branchId' => 'numeric',
            'customerId' => 'numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'startMonth' => 'date_format:m',
            'endMonth' => 'date_format:m',
            'startYear' => 'date_format:Y',
            'endYear' => 'date_format:Y',
            'withoutPagination' => 'boolean'
        ];
    }
}
