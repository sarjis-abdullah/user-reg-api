<?php

namespace App\Http\Resources;

use App\Models\StockLog;
use App\Models\StockTransferProduct;
use Illuminate\Http\Request;

class StockLogResource extends Resource
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
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'sl.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'referenceNumber' => $this->referenceNumber,
            'logReference' => $this->logReference(),
            'type' => $this->type,
            'resourceId' => $this->resourceId,
            'resource' => $this->when($this->needToInclude($request, 'sl.resource'), function () {
                return $this->getResourceByType();
            }),
            'stockId' => $this->stockId,
            'stock' => $this->when($this->needToInclude($request, 'sl.stock'), function () {
                return new StockResource($this->stock);
            }),
            'productId' => $this->productId,
            'product' => $this->when($this->needToInclude($request, 'sl.product'), function () {
                return new ProductResource($this->product);
            }),
            'profitAmount' => $this->profitAmount,
            'discountAmount' => $this->discountAmount,
            'quantity' => $this->quantity,
            'prevQuantity' => $this->prevQuantity,
            'newQuantity' => $this->newQuantity,
            'prevUnitCost' => $this->prevUnitCost,
            'newUnitCost' => $this->newUnitCost,
            'prevUnitPrice' => $this->prevUnitPrice,
            'newUnitPrice' => $this->newUnitPrice,
            'prevExpiredDate' => $this->prevExpiredDate,
            'newExpiredDate' => $this->newExpiredDate,
            'date' => $this->date,
            'receivedBy' => $this->receivedBy,
            'note' => $this->note,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'sl.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }


    /**
     * stock log has different types,
     * get the relationship class by types
     *
     * @return AdjustmentResource|OrderProductResource|OrderProductReturnResource|PurchaseProductResource|PurchaseProductReturnResource|StockTransferResource
     */
    private function getResourceByType()
    {
        $resource = null;

        switch ($this->type) {
            case StockLog::TYPE_ADJUSTMENT_PRODUCT_DECREMENT:
            case StockLog::TYPE_ADJUSTMENT_PRODUCT_INCREMENT:
                $resource = new AdjustmentResource($this->detailByType);
                break;
            case StockLog::TYPE_STOCK_TRANSFER_TO_BRANCH:
            case StockLog::TYPE_STOCK_TRANSFER_FROM_BRANCH:
            case StockLog::TYPE_STOCK_TRANSFER_REVERT_FROM_BRANCH:
                $resource = new StockTransferProductResource($this->detailByType);
                break;
            case StockLog::TYPE_PURCHASE_PRODUCT:
                $resource = new PurchaseProductResource($this->detailByType);
                break;
            case StockLog::TYPE_PURCHASE_PRODUCT_RETURN:
                $resource = new PurchaseProductReturnResource($this->detailByType);
                break;
            case StockLog::TYPE_ORDER_PRODUCT:
                $resource = new OrderProductResource($this->detailByType);
                break;
            case StockLog::TYPE_ORDER_PRODUCT_RETURN:
                $resource = new OrderProductReturnResource($this->detailByType);
                break;
            case StockLog::TYPE_UNIT_PRICE_CHANGED:
            case StockLog::TYPE_UNIT_COST_CHANGED:
                $resource = new StockResource($this->detailByType);
                break;
        }

        return $resource;
    }

    /**
     * @return mixed|null
     */
    public function logReference()
    {
        $resource = null;

        switch ($this->type) {
            case StockLog::TYPE_STOCK_TRANSFER_TO_BRANCH:
            case StockLog::TYPE_STOCK_TRANSFER_FROM_BRANCH:
            case StockLog::TYPE_STOCK_TRANSFER_REVERT_FROM_BRANCH:
                /*$resource = new StockTransferProductResource($this->detailByType);*/
                $resource = optional($this->detailByType)->stockTransferId;
                break;
            case StockLog::TYPE_PURCHASE_PRODUCT:
                /*$resource = new PurchaseProductResource($this->detailByType);*/
                $resource = optional($this->detailByType)->purchaseId;
                break;
            case StockLog::TYPE_PURCHASE_PRODUCT_RETURN:
                /*$resource = new PurchaseProductReturnResource($this->detailByType);*/
                $resource = optional($this->detailByType)->purchaseId;
                break;
            case StockLog::TYPE_ORDER_PRODUCT:
                /*$resource = new OrderProductResource($this->detailByType);*/
                $resource = optional($this->detailByType)->orderId;
                break;
            case StockLog::TYPE_ORDER_PRODUCT_RETURN:
                /*$resource = new OrderProductReturnResource($this->detailByType);*/
                $resource = optional($this->detailByType)->orderId;
                break;
        }

        return $resource;
    }
}
