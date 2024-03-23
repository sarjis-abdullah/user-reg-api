<?php

namespace App\Http\Requests\Quotation;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'branchId' => 'exists:branches,id',
            'customerId' => 'exists:customers,id',
            'amount' => 'sometimes|required|numeric',
            'discount' => 'nullable|numeric',
            'shippingCost' => 'nullable|numeric',
            'note' => 'nullable|string',
            'products' => 'sometimes|required|array',
            'products.*.stockId' => 'sometimes|required|exists:stocks,id',
            'products.*.productId' => 'sometimes|required|exists:products,id',
        ];
    }
}
