<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderProductReturnsGroupByDateResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'order.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'order.company'), function () {
                return new CompanyResource($this->company);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'order.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'customerId' => $this->customerId,
            'customer' => $this->when($this->needToInclude($request, 'order.customer'), function () {
                return new CustomerResource($this->customer);
            }),
            'orderProductReturns' => $this->when($this->needToInclude($request, 'order.orderProductReturns'), function () {
                return OrderProductReturnResource::collection($this->orderProductReturns);
            }),
            'date' => $this->date,
            'terminal' => $this->terminal,
            'invoice' => $this->invoice,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'shippingCost' => $this->shippingCost,
            'totalReturnAmount' => count($this->orderProductReturns) ? $this->orderProductReturns->sum('returnAmount') : 0,
            'amount' => $this->amount,
            'paid' => $this->paid,
            'due' => $this->due,
            'status' => $this->status,
            'comment' => $this->comment,
            'paymentMethods' => $this->paymentMethods(),
            'paymentStatus' => $this->paymentStatus,
            'deliveryMethod' => $this->deliveryMethod,
            'updatedByUserId' => $this->updatedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
