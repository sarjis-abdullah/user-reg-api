<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\Request;
use App\Models\Supplier;
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
        $userId = $this->segment(4);

        return [
            'updatedByUserId' => 'exists:users,id',
            'companyId' => 'exists:companies,id',
            'branchId' => 'exists:branches,id',
            'name' => 'min:2',
            'agencyName' => 'min:2',
            'type' => 'in:'. implode(',', Supplier::getConstantsByPrefix('TYPE_')),
            'status' => 'in:'. implode(',', Supplier::getConstantsByPrefix('STATUS_')),
            'email' => Rule::unique('suppliers')->ignore($userId, 'id'),
            'phone' => [Rule::unique('suppliers')->ignore($userId, 'id')],
            'address' => '',
        ];
    }
}
