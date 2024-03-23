<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CouponCustomerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'couponId' => $this->couponId,
            'coupon' => $this->when($this->needToInclude($request, 'cc.coupon'), function () {
                return new CouponResource($this->coupon);
            }),
            'customerId' => $this->customerId,
            'customer' => $this->when($this->needToInclude($request, 'cc.customer'), function () {
                return new CustomerResource($this->customer);
            }),
            'group' => $this->group,
            'couponUsage' => $this->couponUsage,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
