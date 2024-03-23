<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\Request;
use App\Models\Payment;
use App\Rules\PaymentResourceAmountValidate;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payType' => 'in:' . implode(',', Payment::getConstantsByPrefix('PAY_TYPE_')),
            'paymentableId' => ['required_with:paymentableType', new PaymentResourceAmountValidate($this->get('amount'), $this->get('paymentableType'))],
            'paymentableType' => 'required_with:paymentableId|in:' . implode(',', Payment::getConstantsByPrefix('PAYMENT_SOURCE_')),
            'cashFlow' => 'required|in:' . implode(',', Payment::getConstantsByPrefix('CASH_FLOW_')),
            'method' => 'in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'amount' => 'required|numeric',
            'redeemedPoints' => 'required_if:method,'.Payment::METHOD_LOYALTY_REWARD.'|numeric',
            'changedAmount' => 'numeric',
            'receivedAmount' => 'numeric',
            'txnNumber' => 'string',
            'referenceNumber' => 'string',
            'date' => 'date_format:Y-m-d',
            'status' => 'in:' . implode(',', Payment::getConstantsByPrefix('STATUS_')),
            'receiveByUserId' => 'exists:users,id',
            'createdByUserId' => 'exists:users,id'
        ];
    }
}
