<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Resources\Json\JsonResource;

class DailySummaryResource extends JsonResource
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
            'totalSaleInAmount' => round($this->resource['totalSaleInAmount'], 2),
            'totalReturnSaleInAmount' => round($this->resource['totalReturnSaleInAmount'], 2),
            'totalPurchaseInAmount' => round($this->resource['totalPurchaseInAmount'], 2),
            'totalReturnPurchaseInAmount' => round($this->resource['totalReturnPurchaseInAmount'], 2),
            'totalExpenseInAmount' => round($this->resource['totalExpenseInAmount'], 2),
            'totalIncomeInAmount' => round($this->resource['totalIncomeInAmount'], 2),
            'totalSaleDueAmount' => round($this->resource['totalSaleDueAmount'], 2),
            'todayTotalSaleDuePaymentAmount' => round($this->resource['todayTotalSaleDuePaymentAmount'], 2),
            'totalPurchaseDueAmount' => round($this->resource['totalPurchaseDueAmount'], 2),
            'totalSaleDuePaymentAmount' => round($this->resource['totalSaleDuePaymentAmount'], 2),
            'totalPurchaseDuePaymentAmount' => round($this->resource['totalPurchaseDuePaymentAmount'], 2),
            'totalSaleReturnAmount' => round($this->resource['totalSaleReturnAmount'], 2),
            'totalPurchaseReturnAmount' => round($this->resource['totalPurchaseReturnAmount'], 2),
            'totalNetSales' => round($this->getNetSales(),2),
            'totalNetPurchases' => round($this->getNetPurchase(),2),
            'totalGrossIncome' =>  round($this->getGrossIncome(),2),
            'totalNetEarnings' =>  round($this->getTotalNetEarnings(),2),
        ];
    }

    /**
     * @return float
     */
    public function getNetSales(): float
    {
        return round($this->resource['totalSaleInAmount'], 2) - round($this->resource['totalSaleReturnAmount'], 2);
    }

    /**
     * @return float
     */
    public function getNetPurchase(): float
    {
        return round($this->resource['totalPurchaseInAmount'], 2) - round($this->resource['totalReturnPurchaseInAmount'], 2);
    }

    /**
     * @return float
     */
    public function getGrossIncome(): float
    {
       return $this->getNetSales() + round($this->resource['totalIncomeInAmount'], 2);
    }

    /**
     * @return float
     */
    public function getTotalNetEarnings(): float
    {
        return $this->getGrossIncome() - $this->getNetPurchase() - round($this->resource['totalExpenseInAmount'], 2);
    }

}
