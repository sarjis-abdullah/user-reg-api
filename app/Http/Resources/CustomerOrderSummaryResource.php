<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CustomerOrderSummaryResource extends Resource
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
            "customerId" => $this->resource['customerId'],
            "branchId" => $this->resource['branchId'],
            "totalAmount" => $this->resource['totalAmount'],
            "totalDue" => $this->resource['totalDue'],
            "totalPaid" => $this->resource['totalPaid'],
            "totalShippingCost" => $this->resource['totalShippingCost'],
            "totalDiscount" => $this->resource['totalDiscount'],
            "totalTax" => $this->resource['totalTax']
        ];
    }
}
