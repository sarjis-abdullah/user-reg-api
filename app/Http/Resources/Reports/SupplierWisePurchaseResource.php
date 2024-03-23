<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;
use App\Models\Payment;

class SupplierWisePurchaseResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'supplierId' => $this->resource->supplierId,
            'branchId' => $this->resource->branchId,
            'supplierName' => $this->resource->supplierName,
            'branchName' => $this->resource->branchName,
            'totalPurchase' => $this->resource->totalPurchase,
            'totalReturnAmount' => round($this->resource->totalReturnAmount, 2),
            'totalPurchaseAmount' => round($this->resource->totalPurchaseAmount, 2),
            'totalDiscountAmount' => round($this->resource->totalDiscountAmount, 2),
            'totalTaxAmount' => round($this->resource->totalTaxAmount, 2),
            'totalPaidAmount' => round($this->resource->totalPaidAmount, 2),
            'totalDueAmount' => round($this->resource->totalDueAmount, 2),
            'paymentStatus' => $this->getPaymentStatus()
        ];
    }

    /**
     * @return string
     */
    public function getPaymentStatus(): string
    {
        $paid = round($this->resource->totalPaidAmount,2);
        $due = round($this->resource->totalDueAmount,2);
        return Payment::paymentStatus($due,$paid);
    }


}
