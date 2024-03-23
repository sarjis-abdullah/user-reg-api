<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
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
            'totalCustomers' => $this->resource->totalCustomers,
            'totalOrders' => $this->resource->totalOrders,
            'totalSuppliers' => $this->resource->totalSuppliers,
            'totalPurchases' => $this->resource->totalPurchases,
            'totalProducts' => $this->resource->totalProducts,
            'totalSaleInAmount' => round($this->resource->totalSaleInAmount, 2),
            'totalPurchaseInAmount' => round($this->resource->totalPurchaseInAmount, 2),
            'totalReturnSaleInAmount' => round($this->resource->totalReturnSaleInAmount, 2),
            'totalExpenseInAmount' => round($this->resource->totalExpenseInAmount, 2),
            'totalCustomerDueInAmount' => round($this->resource->totalCustomerDueInAmount, 2),
            'totalNetSalesInAmount' => round($this->resource->totalSaleInAmount, 2) - round($this->resource->totalReturnSaleInAmount, 2),
        ];
    }
}
