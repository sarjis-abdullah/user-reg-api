<?php

namespace App\Http\Resources;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponResource extends Resource
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
            'id'=> $this->id,
            'createdByUserId'=> $this->createdByUserId,
            'title'=> $this->title,
            'code'=> $this->code,
            'description'=> $this->description,
            'to'=> $this->to,
            'type'=> $this->type,
            'amount'=> $this->amount,
            'amountType'=> $this->amountType,
            'minTxnAmount'=> $this->minTxnAmount,
            'maxDiscountAmount'=> $this->maxDiscountAmount,
            'usedIn'=> $this->usedIn,
            'maxCouponUsage'=> $this->maxCouponUsage,
            'startDate'=> $this->startDate,
            'expirationDate'=> $this->expirationDate,
            'status'=> $this->status,
            'customerIds' => $this->to == Coupon::TO_INDIVIDUAL_CUSTOMER ? $this->couponCustomers->pluck('customerId')->toArray() : null,
            'groups' => $this->to == Coupon::TO_GROUP_CUSTOMER ? $this->couponCustomers->pluck('group')->toArray() : null,
            'updatedByUserId'=> $this->updatedByUserId,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
