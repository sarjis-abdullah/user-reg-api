<?php

namespace App\Http\Requests\Branch;

use App\Http\Requests\Request;
use App\Models\Branch;

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
            'name' => '',
            'address' => '',
            'email' => '',
            'phone' => '',
            'details' => '',
            'status' => 'in:'. implode(',', Branch::getConstantsByPrefix('STATUS_')),
            'type' => 'list:string',
            'updatedByUserId' => '',
            'withoutPagination' => 'boolean',
            'query' => 'string',
        ];
    }
}
