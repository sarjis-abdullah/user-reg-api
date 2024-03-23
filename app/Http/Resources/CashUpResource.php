<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CashUpResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'cu.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'cu.company'), function () {
                return new CompanyResource($this->company);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'cu.branch'), function () {
                return new BrandResource($this->brand);
            }),
            'openedDate' => $this->openedDate,
            'openedBy' => $this->openedBy,
            'openedCash' => $this->openedCash,
            'expectedCash' => $this->getExpectedCashBranchWise(),
            'cashIn' => $this->cashIn,
            'cashOut' => $this->cashOut,
            'closedCash' => $this->closedCash,
            'closedDate' => $this->closedDate,
            'closedBy' => $this->closedBy,
            'openedNotes' => $this->openedNotes,
            'closedNotes' => $this->closedNotes,
            'dues' => $this->dues,
            'cards' => $this->cards,
            'cheques' => $this->cheques,
            'mBanking' => $this->mBanking,
            'total' => $this->total,
            'status' => $this->status,
            'updatedByUserId' => $this->updatedByUserId,
            'cashDifference' => round($this->closedCash - $this->openedCash, 2),
            'updatedByUser' => $this->when($this->needToInclude($request, 'cu.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
