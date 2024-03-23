<?php

namespace App\Http\Requests\Expense;

use App\Http\Requests\Request;
use App\Models\Payment;

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
            'updatedByUserId' => 'exists:users,id',
            'branchId' => 'exists:branches,id',
            'categoryId' => 'exists:expense_categories,id',
            'amount' => '',
            'paymentType' => 'min:2',
            'expenseReason' => 'min:2',
            'expenseDate' => 'date_format:Y-m-d',
            'notes' => 'min:2',
            'payment' => "",
            'payment.amount' => 'numeric',
            'payment.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payment.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
        ];
    }
}
