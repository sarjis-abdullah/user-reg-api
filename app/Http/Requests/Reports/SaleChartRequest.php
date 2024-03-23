<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class SaleChartRequest extends Request
{
    public function rules(): array
    {
        return [
            "month" => "date_format:Y-m",
            "year" => "date_format:Y",
            "dataOf" =>"required|in:today,this_week,this_month,last_month,this_year,all"
        ];
    }
}
