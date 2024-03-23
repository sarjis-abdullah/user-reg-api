<?php

namespace App\Http\Requests\UserRoleModulePermission;

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
            'branchId' => 'exists:branches,id',
            'userId' => 'exists:users,id',
            'roleId' => 'exists:roles,id',
            "moduleActionNames" => "array|min:1",
            "moduleActionNames.*" => "string|distinct",
            "moduleActionIds" => "array|min:1",
            "moduleActionIds.*" => "numeric|distinct",
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
