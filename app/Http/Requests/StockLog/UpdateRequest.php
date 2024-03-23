<?php

namespace App\Http\Requests\StockLog;

use App\Http\Requests\Request;

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
            'referenceNumber' => 'string',
            'resourceId' => 'numeric',
            'type' => 'string',
            'stockId' => 'exists:stocks,id',
            'productId' => 'exists:products,id',
            'newQuantity' => 'numeric',
            'date' => 'date_format:Y-m-d',
            'receivedBy' => 'string',
            'note' => 'string|nullable',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
