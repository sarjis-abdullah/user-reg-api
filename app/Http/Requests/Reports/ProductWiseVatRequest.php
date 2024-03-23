<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class ProductWiseVatRequest extends Request
{
    public function rules(): array
    {
        return [
            "taxId" => "numeric",
            "branchId" => "numeric",
            "startDate" => "date_format:Y-m-d",
            "endDate" => "date_format:Y-m-d",
            'withoutPagination' => 'boolean'
        ];
    }
}
