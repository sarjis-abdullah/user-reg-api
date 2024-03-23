<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CustomerOrderReturnSummaryResource extends Resource
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
            "totalReturnAmount" => $this->resource['totalReturnAmount'],
            "totalReturnQuantity" => $this->resource['totalReturnQuantity'],
            "totalReturn" => $this->resource['totalReturn']
        ];
    }
}
