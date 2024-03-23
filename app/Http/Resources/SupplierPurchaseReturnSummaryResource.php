<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SupplierPurchaseReturnSummaryResource extends Resource
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
            "totalReturnAmount" => $this->resource['totalReturnAmount'],
            "totalReturnQuantity" => $this->resource['totalReturnQuantity'],
            "totalReturn" => $this->resource['totalReturn']
        ];
    }
}
