<?php

namespace App\Http\Requests\DeliveryAgency;

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
            'name' => 'string',
            'phone' => 'string',
            'email' => 'string',
            'contactPerson' => 'string',
            'withoutPagination' => 'sometimes|integer',
            'query' => 'string',
        ];
    }
}
