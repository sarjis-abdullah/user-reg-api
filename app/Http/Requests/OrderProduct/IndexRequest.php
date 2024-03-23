<?php

namespace App\Http\Requests\OrderProduct;

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
            'id' => 'list:numeric',
            'createdByUserId' => 'list:numeric',
            'productId' => 'list:numeric',
            'orderId' => 'list:numeric',
            'stockId' => 'list:numeric',
            'unitPrice' => 'numeric',
            'discountedUnitPrice' => 'numeric',
            'quantity' => 'numeric',
            'amount' => 'numeric',
            'color' => '',
            'size' => '',
            'tax' => 'numeric',
            'taxId' => 'numeric',
            'discount' => 'numeric',
            'discountId' => 'numeric',
            'status' => 'in:',
            'updatedByUserId' => 'list:numeric',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
