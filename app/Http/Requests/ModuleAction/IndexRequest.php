<?php

namespace App\Http\Requests\ModuleAction;

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
            'moduleId' => 'list:numeric',
            'name' => 'string',
            'hasAccessUpToRoleId' => 'list:numeric',
            'updatedByUserId' => 'list:numeric',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
