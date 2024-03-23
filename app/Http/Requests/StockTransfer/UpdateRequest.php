<?php

namespace App\Http\Requests\StockTransfer;

use App\Http\Requests\Request;
use App\Models\StockTransfer;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'deliveryMethod' => 'string',
            'sendingNote' => '',
            'receivedNote' => '',
            'status' => 'in:' . StockTransfer::STATUS_PENDING . ',' . StockTransfer::STATUS_CANCELLED . ',' . StockTransfer::STATUS_DECLINED . ',' . StockTransfer::STATUS_SHIPPED . ',' . StockTransfer::STATUS_RECEIVED,
            'shippingCost'=> 'nullable|numeric',
            'shippedByUserId'=> 'sometimes|required|exists:users,id',
            'deliveryAgencyId' => 'exists:delivery_agencies,id',
            'deliveryPersonName' => '',
            'deliveryPersonId'=> 'sometimes|required|exists:users,id',
            'deliveryTrackingNumber' => '',
            'fromDeliveryPhone' => '',

        ];
    }
}
