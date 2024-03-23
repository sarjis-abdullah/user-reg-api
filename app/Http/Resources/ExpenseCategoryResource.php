<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ExpenseCategoryResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'ec.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'branchId'=> $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'ec.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'name' => $this->name,
            'description' => $this->description,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'ec.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
