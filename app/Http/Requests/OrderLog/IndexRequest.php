<?php

namespace App\Http\Requests\OrderLog;

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
            'orderId' => 'list:numeric',
            'status' => 'list:string',
            'paymentStatus' => 'list:string',
            'deliveryStatus' => 'list:string',
            'createdByUserId' => 'list:numeric',
            'updatedByUserId' => 'list:numeric',
        ];
    }
}
