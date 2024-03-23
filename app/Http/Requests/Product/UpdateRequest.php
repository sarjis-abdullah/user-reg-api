<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\Request;
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
        $id = $this->segment(4);
        return [
            'createdByUserId' => 'nullable|exists:users,id',
            'companyId' => 'nullable|exists:companies,id',
            'categoryId' => 'nullable|exists:categories,id',
            'subCategoryId' => 'nullable|numeric',
            'brandId' => 'nullable|exists:brands,id',
            'unitId' => 'nullable|exists:units,id',
            'departmentId' => 'nullable|exists:departments,id',
            'subDepartmentId' => 'nullable|exists:sub_departments,id',
            'name' => 'min:2',
            'genericName' => 'nullable|min:3',
            'selfNumber' => 'nullable|min:1',
//            'barcodeType' => 'required_with:barcode|in:' . implode(',' , Product::getConstantsByPrefix('BARCODE_TYPE_')),
//            'barcode' => ['required_with:barcodeType', 'numeric', new BarcodeLengthValidation($this->get('barcodeType'))],
            'barcode' => 'sometimes|required|unique:products,barcode,'.$id,
            'discountId' => 'nullable|exists:discounts,id',
            'isDiscountApplicable' => 'boolean',
            'isSerialNumberApplicable' => 'boolean',
            'taxId' => 'nullable|exists:taxes,id',
            'description' => 'nullable',
            'status' => 'nullable',
            'alertQuantity' => 'nullable|numeric',

            'variationOrder' => 'required_with:variations|string|max:128',
            'variations' => 'array',
            'variations.*.size' => 'required|string|max:20',
            'variations.*.color' => 'required|string|max:20',
            'variations.*.material' => 'nullable|string|max:20',
        ];
    }
}
