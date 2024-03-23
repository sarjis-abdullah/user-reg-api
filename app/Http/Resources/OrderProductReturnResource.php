<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderProductReturnResource extends Resource
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
            'id' => $this->id,
            'createdByUserId' =>  $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'opr.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'branchId' =>  $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'opr.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'orderId' =>  $this->orderId,
            'order' => $this->when($this->needToInclude($request, 'opr.order'), function () {
                return new OrderResource($this->order());
            }),
            'customer' => $this->when($this->needToInclude($request, 'opr.customer'), function () {
                return new CustomerResource($this->customer());
            }),
            'product' => $this->when($this->needToInclude($request, 'opr.product'), function () {
                return new ProductResource($this->productById);
            }),
            'orderProductId' =>  $this->orderProductId,
            'orderProductName' =>  optional($this->productById)->name,
            'orderProduct' => $this->when($this->needToInclude($request, 'opr.orderProduct'), function () {
                return new OrderProductResource($this->orderProduct);
            }),
            'date' =>  $this->date,
            'comment' => $this->comment,
            'quantity' => $this->quantity,
            'unitPrice' => optional($this->orderProduct)->unitPrice,
            'returnAmount' => $this->returnAmount,
            'profitAmount' => $this->profitAmount,
            'discountAmount' => $this->discountAmount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
