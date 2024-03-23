<?php

namespace App\Http\Resources;


use App\Models\Payment;
use Illuminate\Http\Request;
use function PHPUnit\Framework\matches;

class PaymentSummaryResource extends Resource
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
            'paymentType' => $this->cashFlow == 'in' ? Payment::PAYMENT_TYPE_CREDIT : Payment::PAYMENT_TYPE_DEBIT,
            'paymentSource' => $this->paymentSource($this->paymentableType),
            'payType' => $this->payType,
            'method' => $this->method,
            'amount' => $this->amount,
            'receivedAmount' => $this->receivedAmount,
            'changedAmount' => $this->changedAmount,
            'txnNumber' => $this->txnNumber,
            'date' => $this->date,
            'status' => $this->status,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'payment.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
        ];
    }

    /**
     * @param $paymentAbleType
     * @return string
     */
    public function paymentSource($paymentAbleType): string
    {
        return match ($paymentAbleType) {
            Payment::PAYMENT_SOURCE_ORDER                   => 'Sale',
            Payment::PAYMENT_SOURCE_ORDER_DUE               => 'Sale Due',
            Payment::PAYMENT_SOURCE_PURCHASE                => 'Purchase',
            Payment::PAYMENT_SOURCE_PURCHASE_DUE            => 'Purchase Due',
            Payment::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN    => 'Sale Return',
            Payment::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN => 'Purchase Return',
            Payment::PAYMENT_SOURCE_INCOME                  => 'Income',
            Payment::PAYMENT_SOURCE_EXPENSE                 => 'Expense',
        };
    }
}
