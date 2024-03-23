<?php

namespace App\Http\Requests\StockTransfer;

use App\Http\Requests\Request;
use App\Models\Branch;
use App\Models\StockTransfer;

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
            'fromBranchId' => 'required|exists:branches,id',
            'toBranchId' => 'required|exists:branches,id',
            'sendingNote' => '',
            'status' => 'in:' . StockTransfer::STATUS_PENDING . ',' . StockTransfer::STATUS_CANCELLED . ',' . StockTransfer::STATUS_DECLINED . ',' . StockTransfer::STATUS_SHIPPED . ',' . StockTransfer::STATUS_RECEIVED,

            'products' => 'required|array|min:1',
            'products.*.productId' => 'required:productId|exists:products,id',
            'products.*.quantity' => 'required:productId|numeric',
            'products.*.sku' => 'required:productId|string',
            'products.*.unitCostToBranch' => 'required|numeric',
            'products.*.totalAmount' => '',

            'shippingCost'=> 'nullable|numeric',
            'deliveryAgencyId' => 'exists:delivery_agencies,id',
            'deliveryMethod' => 'string',
            'deliveryNote' => '',
            'deliveryPersonName' => '',
            'deliveryPersonId'=> 'sometimes|required|exists:users,id',
            'shippedByUserId'=> 'sometimes|required|exists:users,id',
            'deliveryTrackingNumber' => '',
            'fromDeliveryPhone' => '',
        ];

//        if(Branch::where('id', $this->input('toBranchId'))->where('type', Branch::TYPE_FRANCHISE)->exists()) {
//            $rules['products.*.increaseCostPriceAmount'] = [
//                'numeric',
//                'between:0,99.99',
//            ];
//        }

        return $rules;
    }
}
