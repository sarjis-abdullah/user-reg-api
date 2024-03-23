<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\Request;

class UpdateComboRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'productId' => 'exists:products,id',
            'branchId' => 'exists:branches,id',
            'status' => 'string',
            'quantity' => 'numeric',
            'sku' => 'string',
            'alertQuantity' => 'numeric',
            'unitCost' => 'numeric',
            'unitPrice' => 'numeric',
            'expiredDate' => 'nullable|date_format:Y-m-d',
            'updatedByUserId' => 'exists:users,id',
            'size' => 'string',
            'color' => 'string',
            'material' => 'string',
        ];

        if($this->has('bundleOfferPromoterProducts')){
            $rules = array_merge($rules, [
                'bundleOfferPromoterProducts' => 'required',
                'bundleOfferPromoterProducts.*.productId' => 'required',
                'bundleOfferPromoterProducts.*.freezQuantity' => 'required|integer|min:1',
                'bundleOfferPromoterProducts.*.stockId' => 'required',

                'bundleOfferProducts' => 'required',
                'bundleOfferProducts.*.productId' => 'required',
                'bundleOfferProducts.*.freezQuantity' => 'required|integer|min:1',
                'bundleOfferProducts.*.stockId' => 'required',
//                'bundle.offerProducts'  =>  ['prohibited_unless:bundle.offerAmount,null,','required_without:bundle.offerAmount'],
//                'bundle.offerAmount'  =>  ['prohibited_unless:bundle.offerProducts,null,','required_without:bundle.offerProducts'],
            ]);
        }

        return $rules;
    }
}
