<?php

namespace App\Http\Requests\Branch;

use App\Http\Requests\Request;
use App\Models\Branch;
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
        $branchId = $this->segment(4);

        return [
            'companyId' => 'exists:companies,id',
            'name' => 'min:3',
            'address' => 'min:5',
            'email' => 'email',
            'phone' => 'max:20',
            'details' => '',
            'status' => 'in:'. implode(',', Branch::getConstantsByPrefix('STATUS_')),
            'updatedByUserId' => 'exists:users,id',
            'type' => [
                'in:' . implode(',', Branch::getConstantsByPrefix('TYPE_')),
                Rule::unique('branches')->where(function ($query) use ($branchId) {
                    return $query->where('type', Branch::TYPE_ECOMMERCE)->where('id', '<>', $branchId);
                }),
            ],
        ];
    }
}
