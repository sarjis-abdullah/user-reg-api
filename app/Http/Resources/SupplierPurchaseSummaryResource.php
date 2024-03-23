<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierPurchaseSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "supplierId" => $this->resource['supplierId'],
            "totalAmount" => $this->resource['totalAmount'],
            "totalDue" => $this->resource['totalDue'],
            "totalPaid" => $this->resource['totalPaid'],
            "totalShippingCost" => $this->resource['totalShippingCost'],
            "totalDiscount" => $this->resource['totalDiscount'],
            "totalTax" => $this->resource['totalTax']
        ];
    }
}
