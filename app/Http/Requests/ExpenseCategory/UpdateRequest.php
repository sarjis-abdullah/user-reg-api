<?php

namespace App\Http\Requests\ExpenseCategory;

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
            'updatedByUserId' => 'exists:users,id',
            'branchId' => 'exists:branches,id',
            'name' => 'min:2',
            'description' => 'min:2',
        ];
    }
}
