<?php

namespace App\Http\Requests\CashUp;

use App\Http\Requests\Request;
use App\Models\CashUp;

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
            'createdByUserId' => 'exists:users,id',
            'companyId' => 'exists:companies,id',
            'branchId' => 'exists:branches,id',
            'openedDate' => 'date_format:Y-m-d',
            'openedBy' => 'required',
            'openedCash' => 'numeric',
            'openedNotes' => 'Min:5',
            'cashIn' => 'numeric',
            'cashOut' => 'numeric',
            'closedCash' => 'numeric',
            'closedDate' => 'date_format:Y-m-d',
            'closedBy' => 'min:2',
            'closedNotes' => 'min:5',
            'dues' => 'numeric',
            'cards' => 'numeric',
            'cheques' => 'numeric',
            'mBanking' => 'numeric',
            'total' => 'numeric',
            'status' => 'in:' . implode(',' , CashUp::getConstantsByPrefix('STATUS_')),
            'updatedByUserId' => 'exists:users,id',
        ];
    }
}
