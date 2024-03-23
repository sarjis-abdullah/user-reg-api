<?php

namespace App\Http\Requests\Adjustment;

use App\Http\Requests\Request;
use App\Models\Adjustment;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "branchId" => 'exists:branches,id',
            "stockId" => 'exists:stocks,id',
            "quantity" => 'numeric',
            "reason" => 'string|max:512',
            "date" => 'date_format:Y-m-d',
            "type" => 'in:' . implode(',',Adjustment::getConstantsByPrefix('TYPE_')),
            "adjustmentBy" => 'string|max:256',
            "updatedByUserId" => 'exists:users,id',
        ];
    }
}
