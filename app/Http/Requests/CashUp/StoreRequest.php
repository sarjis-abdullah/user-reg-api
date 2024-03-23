<?php

namespace App\Http\Requests\CashUp;

use App\Http\Requests\Request;
use App\Models\CashUp;

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
            'branchId' => 'required|exists:branches,id',
            'openedDate' => 'required|date_format:Y-m-d',
            'openedBy' => 'required',
            'openedCash' => 'required|numeric',
            'openedNotes' => 'required|Min:5',
            'status' => 'in:' . implode(',' , CashUp::getConstantsByPrefix('STATUS_')),
        ];
    }
}
