<?php

namespace App\Http\Requests\Adjustment;

use App\Http\Requests\Request;
use App\Models\Adjustment;
use Illuminate\Validation\Rule;

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
            "branchId" => 'list:numeric',
            "stockId" => 'list:numeric',
            "quantity" => 'numeric',
            "reason" => 'string|max:512',
            "date" => 'date_format:Y-m-d',
            "type" => 'in:' . implode(',',Adjustment::getConstantsByPrefix('TYPE_')),
            "adjustmentBy" => 'string|max:256',
            "updatedByUserId" => 'list:numeric',
            'query' => 'string',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'withoutPagination' => 'sometimes|integer',
            'withDeletedStock' => 'boolean'
        ];
    }
}
