<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin;
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
            'level' => 'in:' . Admin::LEVEL_LIMITED . ',' . Admin::LEVEL_STANDARD,
            'user' => '',
            'user.name' => 'min:3|max:255',
            'user.locale' => 'in:en,bn',
            'user.email' => 'email',
            'user.phone' => 'max:20',
            'user.isActive' => 'boolean',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
