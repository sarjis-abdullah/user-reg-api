<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\Request;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

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
            'branchId' => 'nullable|exists:branches,id',
            'updatedByUserId' => 'exists:users,id',
            'name' => 'required|min:2',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'unique:customers,phone|max:20',
            'type' => 'nullable|in:'. implode(',', Customer::getConstantsByPrefix('TYPE_')),
            'group' => 'nullable|in:'. implode(',', Customer::getConstantsByPrefix('GROUP_')),
            'status' => 'in:'. implode(',', Customer::getConstantsByPrefix('STATUS_')),
            'address' => 'string|max:128',
            'address2' => 'string|max:128',
            'city' => 'string|max:56',
            'state' => 'string|max:56',
            'postCode' => 'string|max:12',
        ];
    }
}
