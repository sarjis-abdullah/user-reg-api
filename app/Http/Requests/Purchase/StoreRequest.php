<?php

namespace App\Http\Requests\Purchase;

use App\Http\Requests\Request;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'supplierId' => 'required|numeric|exists:suppliers,id',
            'branchId' => 'required|exists:branches,id',
            'status' => 'required|in:'. implode(',', Purchase::getConstantsByPrefix('STATUS_')),
            'totalAmount' => 'required|numeric',
            'discountAmount' => 'nullable|numeric',
            'shippingCost' => 'nullable|numeric',
            'taxAmount' => 'nullable|numeric',
            'note' => 'nullable|string|max:1024',
            'due' => 'numeric',
            'date' => 'required|date_format:Y-m-d',

            'purchaseProducts' => 'required|array',
            'purchaseProducts.*.sku' => 'nullable|string|max:30',
            'purchaseProducts.*.productId' => 'required|exists:products,id',
            'purchaseProducts.*.quantity' => 'required|numeric',
            'purchaseProducts.*.unitCost' => 'required|numeric',
            'purchaseProducts.*.discountedUnitCost' => 'required|numeric',
            'purchaseProducts.*.sellingPrice' => 'required|numeric',
            'purchaseProducts.*.discountAmount' => 'nullable|numeric',
            'purchaseProducts.*.discountType' => 'nullable|string|in:flat,percentage',
            'purchaseProducts.*.taxAmount' => 'nullable|numeric',
            'purchaseProducts.*.totalAmount' => 'required|numeric',
            'purchaseProducts.*.expiredDate' => 'nullable|date_format:Y-m-d',
            'purchaseProducts.*.existingUnitCost' => 'nullable|numeric',
            'purchaseProducts.*.existingDiscount' => 'nullable|numeric',
            'purchaseProducts.*.productVariationId' => 'nullable|exists:product_variations,id',

            'payment' => "",
            'payment.amount' => 'numeric',
            'payment.changedAmount' => 'numeric|nullable',
            'payment.receivedAmount' => 'numeric|nullable',
            'payment.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payment.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
            'payment.txnNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),
            'payment.referenceNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),
        ];
    }
}
