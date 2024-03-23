<?php

namespace App\Http\Requests\OrderProductReturn;

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
            'orderProductId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'orderId' => 'list:numeric',
            'id' => 'list:numeric',
            'date' => 'date_format:Y-m-d',
            'comment' => 'string',
            'quantity' => 'numeric',
            'returnAmount' => 'numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
            'withSummary'=> 'sometimes|boolean',
        ];
    }
}
