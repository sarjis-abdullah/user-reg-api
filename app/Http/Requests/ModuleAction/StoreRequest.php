<?php

namespace App\Http\Requests\ModuleAction;

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
            'moduleId' => 'required|exists:modules,id',
            'name' => 'required|string|min:2|max:255',
            'hasAccessUpToRoleId' => 'exists:roles,id',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
