<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class ProductWiseProfitRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'branchId' => 'numeric',
            'productId' => 'list:numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'isGroupBySku' => 'boolean',
            'categoryId' => 'numeric',
            'withoutPagination' => 'boolean'
        ];
    }
}
