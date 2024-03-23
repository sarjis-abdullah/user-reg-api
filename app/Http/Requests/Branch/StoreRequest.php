<?php

namespace App\Http\Requests\Branch;

use App\Http\Requests\Request;
use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => 'required|min:3',
            'address' => 'required|min:5',
            'email' => 'email',
            'phone' => 'required|max:20',
            'details' => '',
            'status' => 'in:'. implode(',', Branch::getConstantsByPrefix('STATUS_')),
            'type' => [
                'required',
                'in:' . implode(',', Branch::getConstantsByPrefix('TYPE_')),
                Rule::unique('branches')->where(function ($query) {
                    return $query->where('type', Branch::TYPE_ECOMMERCE);
                }),
            ],
        ];
    }
}
