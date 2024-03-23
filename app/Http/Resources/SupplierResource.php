<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SupplierResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'supplier.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'supplier.company'), function () {
                return new CompanyResource($this->company);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'supplier.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'name' => $this->name,
            'agencyName' => $this->agencyName,
            'categories' => $this->categories,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'type' => $this->type,
            'status' => $this->status,
            'paymentStatus' => $this->paymentStatus(),
            'purchases'=> $this->when($this->needToInclude($request, 'supplier.purchases'), function () {
                return PurchaseResource::collection($this->purchases);
            }),
            'purchaseSummary' => $this->when($this->needToInclude($request, 'supplier.purchaseSummary'), function () {
                return new SupplierPurchaseSummaryResource($this->purchaseSummary());
            }),
            'purchaseReturnSummary' => $this->when($this->needToInclude($request, 'supplier.purchaseReturnSummary'), function () {
                return new SupplierPurchaseReturnSummaryResource($this->purchaseReturnSummary());
            }),
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'supplier.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
