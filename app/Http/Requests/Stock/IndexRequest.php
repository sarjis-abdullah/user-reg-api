<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\Request;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'createdByUserId' => 'list:numeric',
            'productId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'sku' => 'string',
            'quantity' => 'numeric',
            'alertQuantity' => 'numeric',
            'unitCost' => 'numeric',
            'unitPrice' => 'numeric',
            'expiredDate' => 'date_format:Y-m-d',
            'status' => '',
            'updatedByUserId' => 'list:numeric',
            'query' => 'string',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'outOfStock' => 'boolean',
            'withoutPagination' => 'sometimes|integer',
            'size' => 'string',
            'color' => 'string',
            'material' => 'string',
        ];
    }
}
