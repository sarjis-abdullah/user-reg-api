<?php

namespace App\Http\Requests\User;

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
        return $rules = [
            'id' => 'list:string',
            'email' => 'list:email',
            'phone' => 'list:string',
            'name' => 'list:string',
            'locale' => 'list:string',
            'query' => 'string',
            'roleId' => 'string',
            'withoutPagination' => 'sometimes|integer',
        ];
    }

}
