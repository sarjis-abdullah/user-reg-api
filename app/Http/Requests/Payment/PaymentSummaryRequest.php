<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\Request;
use App\Models\Payment;

class PaymentSummaryRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'paymentType' => 'in:' . implode(',', Payment::getConstantsByPrefix('PAYMENT_TYPE_')),
            'paymentSource' => 'in:' . implode(',', Payment::getConstantsByPrefix('PAYMENT_SOURCE_')),
            'method' => 'in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'withSummary' => 'boolean',
            'withoutPagination' => 'boolean',
            'branchId' => 'numeric'
        ];
    }
}
