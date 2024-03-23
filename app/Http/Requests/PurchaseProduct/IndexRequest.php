<?php

namespace App\Http\Requests\PurchaseProduct;

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
            'productId' => 'numeric',
            'quantity' => 'numeric',
            'sku' => 'string',
            'unitCost' => 'numeric',
            'discountedUnitCost' => 'numeric',
            'sellingPrice' => 'numeric',
            'discountAmount' => 'numeric',
            'discountType' => 'in:flat,percentage',
            'taxAmount' => 'numeric',
            'totalAmount' => 'numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
