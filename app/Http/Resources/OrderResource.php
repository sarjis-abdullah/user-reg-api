<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class  OrderResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $orderResponse = [
            'id' => $this->id,
            'referenceOrderId' => $this->referenceOrderId,

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

            'salePersonId' => $this->salePersonId,
            'salePerson' => $this->when($this->needToInclude($request, 'order.salePerson'), function () {
                return new EmployeeResource($this->salePerson);
            }),

            'customerName' => $this->customer ? $this->customer->name : 'No customer',

            'loyaltyReward' => $this->loyaltyReward(),

            'orderProducts' => $this->when($this->needToInclude($request, 'order.orderProducts'), function () {
                return OrderProductResource::collection($this->orderProducts);
            }),

            'payments' => $this->when($this->needToInclude($request, 'order.payments'), function () {
                return PaymentResource::collection($this->payments);
            }),

            'invoiceImage' => $this->when($this->needToInclude($request, 'order.invoiceImage'), function () {
                return new AttachmentResource($this->invoiceImage);
            }),

            'orderProductReturns' => $this->when($this->needToInclude($request, 'order.orderProductReturns'), function () {
                return OrderProductReturnResource::collection($this->orderProductReturns);
            }),

            'couponId' => $this->couponId,
            'coupon' => $this->when($this->needToInclude($request, 'order.coupon'), function () {
                return new CouponResource($this->coupon);
            }),

            'logs' => $this->when($this->needToInclude($request, 'order.logs'), function () {
                return OrderLogResource::collection($this->orderLogs);
            }),

            'date' => $this->date ?? $this->created_at,
            'terminal' => $this->terminal,
            'invoice' => $this->invoice,
            'tax' => round($this->tax, 2),
            'discount' => $this->discount,
            'shippingCost' => $this->shippingCost,
            'roundOffAmount' => $this->roundOffAmount,
            'amount' => round($this->amount, 2),
            'profitAmount' => round($this->profitAmount, 2),
            'grossProfit' => round($this->grossProfit, 2),
            'paid' => round($this->paid, 2),

            'rawProductPrice' => $this->totalRawProductPrice(),
            'totalReturnGrossProfit' => round($this->getTotalReturnProfit(), 2),
            'totalReturnAmount' => round($this->getTotalReturnAmount(), 2),

            'status' => $this->status,
            'comment' => $this->comment,
            'paymentMethods' => $this->paymentMethods(),
            'paymentStatus' => $this->paymentStatus,
            'deliveryMethod' => $this->deliveryMethod,

            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'order.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),

            'ecomInvoice' => $this->ecomInvoice,
            'orderUrl' => $this->orderUrk,
            'shipping' => $this->shipping,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->referenceOrderId){
            $orderResponse = array_merge($orderResponse, [
                'totalNetPaidAmount' => round($this->paid),
                'due' => round($this->getDueAmount(), 2),
                'totalNetGrossProfit' => round(($this->grossProfit), 2),
                'totalNetAmount' => round(($this->amount), 2),
            ]);
        }else{
            $orderResponse =  array_merge($orderResponse, [
                'totalNetPaidAmount' => round(($this->paid - $this->getTotalReturnAmount())),
                'due' => round($this->getDueAmount(), 2),
                'totalNetGrossProfit' => round(($this->grossProfit - $this->getTotalReturnProfit()), 2),
                'totalNetAmount' => round(($this->amount - $this->getTotalReturnAmount()), 2),
            ]);
        }

        return $orderResponse;
    }
}
