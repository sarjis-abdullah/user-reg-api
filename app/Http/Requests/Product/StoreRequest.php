<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\Request;
use App\Models\OfferProduct;
use App\Models\Product;
use App\Rules\BarcodeLengthValidation;
use App\Rules\RequiredOneOfMany;
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
            'companyId' => 'nullable|exists:companies,id',
            'categoryId' => 'nullable|exists:categories,id',
            'subCategoryId' => 'nullable|numeric',
            'brandId' => 'nullable|exists:brands,id',
//            'unitId' => 'required_without:bundle|exists:units,id',
            'departmentId' => 'nullable|exists:departments,id',
            'subDepartmentId' => 'nullable|exists:sub_departments,id',
            'name' => 'required|min:3',
            'genericName' => 'nullable|min:3',
            'selfNumber' => 'nullable|min:1',
//            'barcodeType' => 'required_with:barcode|in:' . implode(',' , Product::getConstantsByPrefix('BARCODE_TYPE_')),
//            'barcode' => ['required_with:barcodeType', 'numeric', new BarcodeLengthValidation($this->get('barcodeType'))],
            'barcode' => 'sometimes|required|unique:products,barcode',
            'discountId' => 'nullable|exists:discounts,id',
            'isDiscountApplicable' => 'boolean',
            'taxId' => 'nullable|exists:taxes,id',
            'description' => '',
            'status' => '',
            'alertQuantity' => 'numeric',
            'isSerialNumberApplicable' => 'boolean',

            'variationOrder' => 'required_with:variations|string|max:128',
            'variations' => 'required_with:variationOrder|array',

            'openingStock' => '',
            'openingStock.branchId' => 'required_with:openingStock|exists:branches,id',
            'openingStock.quantity' => 'required_with:openingStock|numeric',
            'openingStock.unitCost' => 'required_with:openingStock|numeric',
            'openingStock.unitPrice' => 'required_with:openingStock|numeric',
            'openingStock.expiredDate' => 'date_format:Y-m-d',
        ];

        if($this->has('bundle')){
            $rules = array_merge($rules, [
                'bundle' => 'required',
                'bundle.offerStartsAt' => 'required',
                'bundle.offerEndsAt' => 'sometimes|required',

                'bundle.offerPromoterProducts' => 'required|array',
                'bundle.offerPromoterProducts.*.quantity' => 'required|numeric',
                'bundle.offerPromoterProducts.*.productId' => 'required|numeric|distinct',

                'bundle.offerProducts.*.quantity' => 'required|numeric',
                'bundle.offerProducts.*.discountType' => ['sometimes', 'required', 'string', Rule::in([OfferProduct::DISCOUNT_TYPE_FREE, OfferProduct::DISCOUNT_TYPE_FLAT, OfferProduct::DISCOUNT_TYPE_PERCENTAGE])],
                'bundle.offerProducts.*.discountAmount' => 'sometimes|required|numeric',
                'bundle.offerProducts.*.productId' => 'required|numeric|distinct',
                'bundle.offerProducts'  =>  ['required'],
//                'bundle.offerProducts'  =>  ['prohibited_unless:bundle.offerAmount,null,','required_without:bundle.offerAmount'],
//                'bundle.offerAmount'  =>  ['prohibited_unless:bundle.offerProducts,null,','required_without:bundle.offerProducts'],
            ]);
        }else {
            $rules = array_merge($rules, [
                'unitId' => 'required|exists:units,id',
            ]);
        }
        if(request()->has('variationOrder')) {
            $variationOrders = explode('/', request()->input('variationOrder'));

            collect($variationOrders)->each(function ($order) use (&$rules) {
                $rules['variations.*.' . $order] = 'required|string|min:1|max:20';
            });
        }

        return $rules;
    }
}
