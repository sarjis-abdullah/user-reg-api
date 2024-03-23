<?php

namespace App\Http\Requests\Quotation;

use App\Http\Requests\Request;
use App\Models\Quotation;

class StoreRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'createdByUserId' => 'exists:users,id',
            'branchId' => 'required|exists:branches,id',
            'customerId' => 'exists:customers,id',
            'amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'shippingCost' => 'nullable|numeric',
            'status' => 'in:' . implode(',', Quotation::getConstantsByPrefix('STATUS_')),
            'note' => 'nullable|string',
            'products' => 'required|array',
            'products.*.stockId' => 'required|exists:stocks,id',
            'products.*.productId' => 'required|exists:products,id',
        ];
    }
}
