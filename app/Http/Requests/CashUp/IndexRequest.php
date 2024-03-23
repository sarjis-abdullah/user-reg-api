<?php

namespace App\Http\Requests\CashUp;

use App\Http\Requests\Request;
use App\Models\CashUp;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'list:numeric',
            'createdByUserId' => 'list:numeric',
            'companyId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'openedDate' => 'date_format:Y-m-d',
            'openedBy' => '',
            'openedCash' => '',
            'cashIn' => '',
            'cashOut' => '',
            'closedCash' => '',
            'closedDate' => 'date_format:Y-m-d',
            'closedBy' => '',
            'openedNotes' => '',
            'closedNotes' => '',
            'dues' => '',
            'cards' => '',
            'cheques' => '',
            'mBanking' => '',
            'total' => '',
            'status' => 'in:' . implode(',' , CashUp::getConstantsByPrefix('STATUS_')),
            'updatedByUserId' => 'list:numeric',
            'withoutPagination' => 'sometimes|integer',
        ];
    }
}
