<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\Request;
use App\Models\Order;
use App\Models\Payment;

class ChangeStatusRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //Todo come to this and fix for big client
        return [
            'status' => 'in:' . implode(',', Order::getConstantsByPrefix('STATUS_')),
            'comment' => 'nullable|string',

            'payments' => "array",
            'payments.*.amount' => 'numeric|gt:0',
            'payments.*.changedAmount' => 'numeric|nullable',
            'payments.*.receivedAmount' => 'numeric|nullable',
            'payments.*.redeemedPoints' => 'required_if:method,'.Payment::METHOD_LOYALTY_REWARD.'|numeric',
            'payments.*.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payments.*.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
            'payments.*.txnNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),
            'payments.*.referenceNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),
        ];
    }
}
