<?php

namespace App\Http\Requests\UserRoleModulePermission;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'createdByUserId' => 'exists:users,id',
            'branchId' => 'exists:users,id',
            'userId' => 'unique:user_role_module_permissions,userId,,NULL,id,deleted_at,NULL',
            'roleId' => 'required|unique:user_role_module_permissions,roleId,NULL,id,deleted_at,NULL',
            "moduleActionNames" => "array|min:1",
            "moduleActionNames.*" => "string|distinct",
            "moduleActionIds" => "array|min:1",
            "moduleActionIds.*" => "numeric|distinct",
        ];
    }
}
