<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\Admin;

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
            'updatedByUserId' => 'list:numeric',
            'userId' =>  'list:numeric',
            'userRoleId' =>  'list:numeric',
            'level' => 'in:' . Admin::LEVEL_LIMITED . ',' . Admin::LEVEL_STANDARD,
            'query' => '',
            'withName' => ''
        ];
    }
}
