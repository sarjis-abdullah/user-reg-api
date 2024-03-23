<?php

namespace App\Http\Requests\OrderProductReturn;

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
            'orderId' => 'required|exists:orders,id',

            'orderProductId' => 'required_without:products|exists:order_products,id',
            'quantity' => 'required_without:products|numeric',

            'products' => 'required_without:orderProductId|array|min:1',
            'products.*.orderProductId' => 'required_without:orderProductId|exists:order_products,id',
            'products.*.quantity' => 'required_without:orderProductId|numeric',
            'products.*.returnAmount' => 'required_without:orderProductId|numeric',

            'payment' => "",
            'payment.amount' => 'numeric',
            'payment.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payment.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
        ];
    }
}
