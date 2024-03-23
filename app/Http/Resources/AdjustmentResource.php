<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class AdjustmentResource extends Resource
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
            "id" => $this->id,
            "branchId" => $this->branchId,
            "stockId" => $this->stockId,
            'stock' => $this->when($this->needToInclude($request, 'adjustment.stock'), function () {
                return new StockResource($this->stock);
            }),
            "product" => new ProductResource($this->product),
            "quantity" => $this->quantity,
            "totalUnitCost" => round(($this->quantity * optional($this->stock)->unitCost), 2),
            "totalSellPrice" => round(($this->quantity * optional($this->stock)->unitPrice), 2),
            "reason" => $this->reason,
            "date" => $this->date,
            "type" => $this->type,
            "adjustmentBy" => $this->adjustmentBy,
            "updatedByUserId" => $this->updatedByUserId,
            "createdByUserId" => $this->createdByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'adjustment.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'createdByUser' => $this->when($this->needToInclude($request, 'adjustment.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'branch' => $this->when($this->needToInclude($request, 'adjustment.branch'), function () {
                return new BranchResource($this->branch);
            }),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
