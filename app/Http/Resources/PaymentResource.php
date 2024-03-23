<?php

namespace App\Http\Resources;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentResource extends Resource
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
            'createdByUserId' => $this->createdByUserId,
            'paymentableId' => $this->paymentableId,
            'payType' => $this->payType,
            'paymentableType' => $this->paymentableType,
            'paymentable' => $this->when($this->needToInclude($request, 'payment.paymentable'), function () {
                return $this->getPaymentableResourceByType();
            }),
            'cashFlow' => $this->cashFlow,
            'method' => $this->method,
            'amount' => $this->amount,
            'receivedAmount' => $this->receivedAmount,
            'changedAmount' => $this->changedAmount,
            'txnNumber' => $this->txnNumber,
            'date' => $this->date,
            'status' => $this->status,
            'receiveByUserId' => $this->receiveByUserId,
            'updatedByUserId' => $this->updatedByUserId,
        ];
    }

    /**
     * get the relationship class by types
     *
     */
    private function getPaymentableResourceByType()
    {
        return match ($this->paymentableType) {
            Payment::PAYMENT_SOURCE_INCOME =>new IncomeResource($this->paymentable),
            Payment::PAYMENT_SOURCE_EXPENSE => new ExpenseResource($this->paymentable),
            Payment::PAYMENT_SOURCE_ORDER => new OrderResource($this->paymentable),
            Payment::PAYMENT_SOURCE_ORDER_DUE => new OrderResource($this->paymentable),
            Payment::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN => new OrderProductReturnResource($this->paymentable),
            Payment::PAYMENT_SOURCE_PURCHASE => new PurchaseResource($this->paymentable),
            Payment::PAYMENT_SOURCE_PURCHASE_DUE => new PurchaseResource($this->paymentable),
            Payment::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN => new PurchaseProductReturnResource($this->paymentable),
            default => null
        };
    }
}
