<?php

namespace App\Http\Requests\Quotation;

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
            'branchId' => 'list:numeric',
            'products'=>'',
            'customerId' => 'list:numeric',
            'invoice' => 'list:string',
            'amount' => 'numeric',
            'discount' => 'numeric',
            'shippingCost' => 'numeric',
            'status' => 'in:',
            'note' => '',
            'updatedByUserId' => 'list:numeric',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'query' => '',
            'withoutPagination'=> 'sometimes|integer',
        ];
    }
}
