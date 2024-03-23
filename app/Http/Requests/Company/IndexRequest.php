<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;
use App\Models\Company;

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
            'website' => '',
            'email' => '',
            'phone' => '',
            'type' => '',
            'details' => '',
            'status' => '',
            'updatedByUserId' => 'list:numeric',
            'query' => 'string',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
