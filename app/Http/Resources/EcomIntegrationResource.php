<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class EcomIntegrationResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'ei.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'ei.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'name' => $this->name,
            'apiUrl' => $this->apiUrl,
            'apiKey' => $this->apiKey,
            'apiSecret' => $this->apiSecret,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'ei.updatedByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
