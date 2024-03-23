<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class CategoryWiseSaleRequest extends Request
{
    public function rules(): array
    {
        return [
            "categoryId" => "numeric",
            "branchId" => "numeric",
            "startDate" => "date_format:Y-m-d",
            "endDate" => "date_format:Y-m-d",
            "per_page" => "numeric",
            'withoutPagination' => 'boolean'
        ];
    }
}
