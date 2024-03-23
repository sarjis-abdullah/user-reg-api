<?php

namespace App\Http\Requests\StockLog;

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
            'referenceNumber' => 'string',
            'productId' => 'list:numeric',
            'stockId' => 'list:numeric',
            'resourceId' => 'list:numeric',
            'type' => 'string',
            'quantity' => '',
            'prevQuantity' => 'numeric',
            'newQuantity' => 'numeric',
            'date' => 'date_format:Y-m-d',
            'note' => '',
            'receivedBy' => '',
            'updatedByUserId' => 'list:numeric',
            'withoutPagination' => 'sometimes|integer',
            "startDate" => "date_format:Y-m-d",
            "endDate" => "date_format:Y-m-d",
            "createdAt" => "date_format:Y-m-d",
            "query" => "string",
            "branchId" => 'numeric'
        ];
    }
}
