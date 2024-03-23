<?php

namespace App\Http\Requests\UserRoleModulePermission;

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
            'userId' => 'list:numeric',
            'roleId' => 'list:numeric',
            'moduleActionNames' => 'array',
            'moduleActionIds' => 'array',
            'updatedByUserId' => 'list:numeric',
        ];
    }
}
