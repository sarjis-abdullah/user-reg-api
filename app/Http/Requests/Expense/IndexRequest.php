<?php

namespace App\Http\Requests\Expense;

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
            'updatedByUserId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'categoryId' => 'list:numeric',
            'amount' => '',
            'paymentType' => '',
            'expenseReason' => '',
            'expenseDate' => 'date_format:Y-m-d',
            'notes' => '',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
            'withSummary' => 'boolean',
        ];
    }
}
