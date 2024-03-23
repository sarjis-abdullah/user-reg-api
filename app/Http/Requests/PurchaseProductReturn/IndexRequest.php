<?php

namespace App\Http\Requests\PurchaseProductReturn;

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
            'createdByUserId' => 'list:numeric',
            'purchaseProductId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'purchaseId' => 'list:numeric',
            'date' => 'date_format:Y-m-d',
            'comment' => 'string',
            'quantity' => 'numeric',
            'returnAmount' => 'numeric',
            'query' => 'string',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'withoutPagination' => 'sometimes|integer',
            'withSummary'=> 'sometimes|boolean',
        ];
    }
}
