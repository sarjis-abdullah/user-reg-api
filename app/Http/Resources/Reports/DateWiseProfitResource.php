<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;
use Illuminate\Http\Request;

class DateWiseProfitResource extends Resource
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
            'date' => $this->resource->date ?? null,
            'year' => $this->resource->year ?? null,
            'month' => $this->resource->month ?? null,
            'totalSales' => $this->resource->totalSales,
            'totalSaleAmount' => round($this->resource->totalSaleAmount,2),
            'totalReturnAmount' => round($this->resource->totalReturnAmount,2),
            'totalPaidAmount' => round($this->resource->totalPaidAmount, 2),
            'totalDueAmount' => round($this->resource->totalDueAmount, 2),
            'totalDiscountAmount' => round($this->resource->totalDiscountAmount, 2),
            'totalTaxAmount' => round($this->resource->totalTaxAmount,2),
            'totalShippingCostAmount' => $this->resource->totalShippingCostAmount,
            'totalProfitAmount' =>  round($this->resource->totalProfitAmount,2),
            'totalReturnProfitAmount' =>  round($this->resource->totalReturnProfitAmount,2),
            'totalReturnDiscountAmount' =>  round($this->resource->totalReturnDiscountAmount,2),
            'totalGrossProfitAmount' => round($this->resource->totalGrossProfitAmount,2),
            'totalNetGrossProfitAmount' => round(($this->resource->totalGrossProfitAmount - ($this->resource->totalReturnProfitAmount - $this->resource->totalReturnDiscountAmount)),2),
            'totalNetAmount' => round($this->resource->totalSaleAmount,2) - round($this->resource->totalReturnAmount,2),
            'totalNetPaidAmount' => round(($this->resource->totalPaidAmount - $this->resource->totalReturnAmount),2),
        ];
    }
}
