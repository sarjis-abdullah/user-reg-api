<?php

namespace App\Http\Requests\Manager;

use App\Http\Requests\Request;
use App\Models\Manager;
use Illuminate\Support\Facades\DB;
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
        $managerId = $this->segment(4);
        $userId = DB::table('managers')->where('id', '=', $managerId)->pluck('userId')->toArray()[0];

        return [
            'userId' => 'exists:users,id',
            'userRoleId' => 'exists:user_roles,id',
            'companyId' => 'exists:companies,id',
            'branchId' => 'exists:branches,id',
            'title' => 'min:3',
            'level' => 'exists:roles,title',
            'updatedByUserId' => 'exists:users,id',

            'user' => '',
            'user.name' => 'min:3|max:255',
            'user.locale' => 'in:en,bn',
            'user.email' => 'max:255|email|unique:users,email,' . $userId . ',id',
            'user.phone' => 'max:255|unique:users,phone,' . $userId . ',id',
            'user.isActive' => 'boolean',
        ];
    }
}
