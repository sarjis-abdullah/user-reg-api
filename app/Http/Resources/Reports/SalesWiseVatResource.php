<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;

class SalesWiseVatResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'companyId' => $this->companyId,
            'branchId' => $this->branchId,
            'salePersonId' => $this->salePersonId,
            'customerId' => $this->customerId,
            'referenceId' => $this->referenceId,
            'date' => $this->date,
            'terminal' => $this->terminal,
            'invoice' => $this->invoice,
            'tax' => $this->tax,
            'shippingCost' => $this->shippingCost,
            'discount' => $this->discount,
            'amount' => $this->amount,
            'profitAmount' => $this->profitAmount,
            'grossProfit' => $this->grossProfit,
            'paid' => $this->paid,
            'due' => $this->due,
            'couponId' => $this->couponId,
            'deliveryMethod' => $this->deliveryMethod,
            'status' => $this->status,
            'paymentStatus' => $this->paymentStatus,
            'comment' => $this->comment,
            'updatedByUserId' => $this->updatedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'customerName' => $this->customerName,
        ];
    }
}
