<?php

namespace App\Http\Requests\PurchaseProductReturn;

use App\Http\Requests\Request;
use App\Models\Payment;

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
            'date' => 'nullable|date_format:Y-m-d',
            'comment' => 'nullable|string',
            'branchId' => 'required|exists:branches,id',
            'purchaseId' => 'required|exists:purchases,id',
            'gettableDueAmount' => 'numeric',

            'purchaseProductId' => 'required_without:products|exists:purchase_products,id',
            'quantity' => 'required_without:products|numeric',

            'products' => 'required_without:purchaseProductId|array|min:1',
            'products.*.purchaseProductId' => 'required_without:purchaseProductId|exists:purchase_products,id',
            'products.*.quantity' => 'required_without:purchaseProductId|numeric',
            'products.*.returnAmount' => 'required_without:purchaseProductId|numeric',

            'payment' => "",
            'payment.amount' => 'numeric',
            'payment.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payment.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
        ];
    }
}
