<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class IncomeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            "createdByUserId" => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'income.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            "categoryId" => $this->categoryId,
            'category' => $this->when($this->needToInclude($request, 'income.category'), function () {
                return new IncomeCategoryResource($this->category);
            }),
            "branchId" => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'income.branch'), function () {
                return new BranchResource($this->branch);
            }),
            "amount" => $this->amount,
            "sourceOfIncome" => $this->sourceOfIncome,
            "date" => $this->date,
            "paymentType" => $this->paymentType,
            "notes" => $this->notes,
            "updatedByUserId" => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'income.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
