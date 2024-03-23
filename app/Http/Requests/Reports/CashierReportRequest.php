<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class CashierReportRequest extends Request
{
    public function rules(): array
    {
        return [
            "cashierId" => "numeric",
            "branchId" => "numeric",
            "startDate" => "required|date_format:Y-m-d",
            "endDate" => "required|date_format:Y-m-d",
            'withoutPagination' => 'boolean'
        ];
    }
}
