<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'productId' => 'exists:products,id',
            'branchId' => 'exists:branches,id',
            'status' => 'string',
            'quantity' => 'numeric',
            'sku' => 'string',
            'alertQuantity' => 'numeric',
            'unitCost' => 'numeric',
            'unitPrice' => 'numeric',
            'expiredDate' => 'nullable|date_format:Y-m-d',
            'updatedByUserId' => 'exists:users,id',
            'size' => 'string',
            'color' => 'string',
            'material' => 'string',
        ];
    }
}
