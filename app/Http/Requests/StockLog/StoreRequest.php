<?php

namespace App\Http\Requests\StockLog;

use App\Http\Requests\Request;

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
            'referenceNumber' => 'string',
            'resourceId' => 'required|numeric',
            'type' => 'required|string',
            'stockId' => 'exists:stocks,id',
            'productId' => 'required|exists:products,id',
            'newQuantity' => 'required|numeric',
            'date' => 'date_format:Y-m-d',
            'receivedBy' => 'string',
            'note' => 'string|nullable',
        ];
    }
}
