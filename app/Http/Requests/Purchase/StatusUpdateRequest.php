<?php

namespace App\Http\Requests\Purchase;

use App\Http\Requests\Request;
use App\Models\Purchase;

class StatusUpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:'. implode(',', Purchase::getConstantsByPrefix('STATUS_')),
        ];
    }
}
