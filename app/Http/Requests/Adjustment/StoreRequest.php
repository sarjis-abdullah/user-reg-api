<?php

namespace App\Http\Requests\Adjustment;

use App\Http\Requests\Request;
use App\Models\Adjustment;
use Illuminate\Validation\Rule;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "branchId" => 'required|exists:branches,id',
            "reason" => 'required|string|max:512',
            "date" => 'required|date_format:Y-m-d',
            "adjustmentBy" => 'string|max:256',
            "createdByUserId" => 'exists:users,id',

            "products" => 'required|array|min:1',
            "products.*.stockId" => 'required|exists:stocks,id',
            "products.*.quantity" => 'required|numeric',
            "products.*.type" => 'required|in:' . implode(',', Adjustment::getConstantsByPrefix('TYPE_')),
        ];
    }
}
