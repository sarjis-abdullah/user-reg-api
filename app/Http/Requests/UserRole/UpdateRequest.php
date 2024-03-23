<?php

namespace App\Http\Requests\UserRole;

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
            'roleId' => 'exists:roles,id',
            'userId' => 'exists:users,id',
            'branchId' => 'exists:branches,id',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
