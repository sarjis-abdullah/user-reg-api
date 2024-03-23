<?php

namespace App\Http\Requests\SubCategory;

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
            'categoryId' => 'exists:categories,id',
            'name' => 'min:3',
            'code' => '',
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
