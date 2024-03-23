<?php

namespace App\Http\Requests\Purchase;

use App\Http\Requests\Request;
use App\Models\Purchase;

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
            'supplierId' => 'numeric',
            'status' => 'in:'. implode(',', Purchase::getConstantsByPrefix('STATUS_')),
            'branchId' => 'exists:branches,id',
            'totalAmount' => 'numeric',
            'discountAmount' => 'numeric',
            'shippingCost' => 'numeric',
            'taxAmount' => 'numeric',
            'note' => 'string',
            'due' => 'numeric',
            'paymentMethod' => 'numeric',
            'date' => 'date_format|Y-m-d',
            'reference' => 'string',

            'purchaseProducts' => 'array',
            'purchaseProducts.*.productId' => 'numeric',
            'purchaseProducts.*.quantity' => 'numeric',
            'purchaseProducts.*.sku' => 'string',
            'purchaseProducts.*.expiredDate' => 'date_format:Y-m-d',
            'purchaseProducts.*.unitCost' => 'numeric',
            'purchaseProducts.*.sellingPrice' => 'numeric',
            'purchaseProducts.*.discountAmount' => 'numeric',
            'purchaseProducts.*.taxAmount' => 'numeric',
            'purchaseProducts.*.totalAmount' => 'numeric',
            'purchaseProducts.*.productVariationId' => 'nullable|numeric',
        ];
    }
}
