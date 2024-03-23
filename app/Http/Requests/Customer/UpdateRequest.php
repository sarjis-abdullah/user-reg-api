<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\Request;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = $this->segment(4);

        return [
            'createdByUserId' => 'exists:users,id',
            'branchId' => 'nullable|exists:branches,id',
            'updatedByUserId' => 'exists:users,id',
            'name' => 'min:2',
            'email' => Rule::unique('users')->ignore($userId, 'id'),
            'phone' => Rule::unique('users')->ignore($userId, 'id'),
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
