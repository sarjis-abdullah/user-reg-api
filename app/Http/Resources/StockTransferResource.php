<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class StockTransferResource extends Resource
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
            'referenceNumber' => $this->referenceNumber,
            'fromBranchId' => $this->fromBranchId,
            'fromBranch' => $this->when($this->needToInclude($request, 'st.fromBranch'), function () {
                return new BranchResource($this->fromBranch);
            }),
            'toBranchId' => $this->toBranchId,
            'toBranch' => $this->when($this->needToInclude($request, 'st.toBranch'), function () {
                return new BranchResource($this->toBranch);
            }),
            'stockTransferProducts' => $this->when($this->needToInclude($request, 'st.stockTransferProducts'), function () {
                return StockTransferProductResource::collection($this->stockTransferProducts);
            }),

            'totalAmount'=> $this->getTotalAmountOfStockTransferProduct(),
            'totalUnitCostAmountFromBranch'=> $this->getUnitCostAmount(),
            'totalUnitCostAmountToBranch'=> $this->getUnitCostAmount(),
            'totalSellingAmount'=> $this->getTotalSellingAmountOfStockTransferProduct(),

            'deliveryMethod' => $this->deliveryMethod,
            'deliveryId' => $this->deliveryId,
            'delivery' => $this->when($this->needToInclude($request, 'st.delivery'), function () {
                return new DeliveryResource($this->delivery);
            }),
            'sendingNote' => $this->sendingNote,
            'receivedNote' => $this->receivedNote,
            'status' => $this->status,
            'shippingCost' => $this->shippingCost,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'st.createdByUserId'), function () {
                return new UserResource($this->createdByUser);
            }),
            'shippedByUserId'=> $this->shippedByUserId,
            'shippedByUser' => $this->when($this->needToInclude($request, 'st.shippedByUser'), function () {
                return new UserResource($this->shippedByUser);
            }),
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'st.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
