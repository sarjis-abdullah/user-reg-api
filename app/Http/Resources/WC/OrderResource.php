<?php

namespace App\Http\Resources\WC;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     */
    public function toArray($request)
    {
        return  [
            ...$this->resource->getAttributes(),
            'createdByUser' => $this->createdByUser,
            'coupon' => $this->coupon,
            'branch' => $this->branch,
            'customer' => $this->customer,
            'salePerson' => $this->salePerson,
            'orderProducts' => $this->orderProducts,
            'payments' => $this->payments,
            'invoiceImage' => $this->invoiceImage,
        ];
    }
}
