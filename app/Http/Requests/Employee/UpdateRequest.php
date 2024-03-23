<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\Request;
use App\Models\Employee;
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
        $employeeId = $this->segment(4);
        $userId = DB::table('employees')->where('id', '=', $employeeId)->pluck('userId')->toArray()[0];

        return [
            'branchId' => 'exists:branches,id',
            'title' => 'min:3',
            'level' => 'required|exists:roles,title',
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
