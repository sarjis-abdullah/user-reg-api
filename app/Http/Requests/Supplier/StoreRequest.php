<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Request;
use App\Models\Supplier;

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
            'companyId' => 'exists:companies,id',
            'branchId' => 'exists:branches,id',
            'name' => 'required|min:2',
            'agencyName' => 'min:2',
            'type' => 'required|in:'. implode(',', Supplier::getConstantsByPrefix('TYPE_')),
            'status' => 'in:'. implode(',', Supplier::getConstantsByPrefix('STATUS_')),
            'email' => 'email|unique:suppliers,email',
            'phone' => 'max:20|unique:suppliers,phone',
            'address' => '',
        ];
    }
}
