<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class SalesPersonReportRequest extends Request
{
    public function rules(): array
    {
        return [
            "salesPersonId" => "numeric",
            "branchId" => "numeric",
            "startDate" => "date_format:Y-m-d",
            "endDate" => "date_format:Y-m-d",
            'withoutPagination' => 'boolean'
        ];
    }
}
