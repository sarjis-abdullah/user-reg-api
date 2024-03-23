<?php

namespace App\Http\Requests\Unit;

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
            'name' => 'required|string',
            'parentId' => 'numeric',
            'isFraction' => 'boolean',
        ];
    }
}
