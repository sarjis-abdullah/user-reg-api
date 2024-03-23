<?php

namespace App\Http\Requests\ModuleAction;

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
            'moduleId' => 'exists:modules,id',
            'name' => 'string|min:2|max:255',
            'hasAccessUpToRoleId' => 'exists:roles,id',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
