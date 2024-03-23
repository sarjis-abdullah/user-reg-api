<?php

namespace App\Http\Requests\SubDepartment;

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
            'name' => 'required|min:2',
            'department_id' => 'required|exists:departments,id',
        ];
    }
}
