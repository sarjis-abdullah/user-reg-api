<?php

namespace App\Http\Requests\ExpenseCategory;

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
            'branchId' => 'exists:branches,id',
            'name' => 'required|min:2',
            'description' => 'min:2',
        ];
    }
}
