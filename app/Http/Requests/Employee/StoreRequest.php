<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\Request;
use App\Models\Employee;

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
            'userRoleId' => 'exists:user_roles,id',
            'companyId' => 'exists:companies,id',
            'branchId' => 'required|exists:branches,id',
            'title' => 'min:3',
            'level' => 'required|exists:roles,title',
            'userId' => 'required_without:user|exists:users,id',
            'user' => 'required_without:userId',
            'user.name' => 'required_without:userId|min:3|max:255',
            'user.email' => 'required_without:userId|email|unique:users,email|max:255',
            'user.phone' => 'required_without:userId|unique:users,phone',
            'user.isActive' => 'required_without:userId|boolean',
            'user.password' => 'required_without:userId|min:5|max:255',
            'user.locale' => 'in:en,bn',
        ];
    }
}
