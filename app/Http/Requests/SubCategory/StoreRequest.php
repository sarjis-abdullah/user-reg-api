<?php

namespace App\Http\Requests\SubCategory;

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
            'categoryId' => 'required|exists:categories,id',
            'name' => 'required|min:2',
            'code' => '',
        ];
    }
}
