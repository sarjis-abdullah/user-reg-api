<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\Request;
use App\Models\OfferProduct;
use Illuminate\Validation\Rule;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'createdByUserId' => 'exists:users,id',
            'productId' => 'required|exists:products,id',
            'branchId' => 'required|exists:branches,id',
            'quantity' => 'required|numeric',
            'sku' => 'sometimes|required|string',
            'alertQuantity' => 'numeric',
            'unitCost' => 'required|numeric',
            'unitPrice' => 'required|numeric',
            'expiredDate' => 'date_format:Y-m-d',
            'status' => 'string',
            'size' => 'string',
            'color' => 'string',
            'material' => 'string'
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
