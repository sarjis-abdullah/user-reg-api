<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;

class BrandResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'brand.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'brand.company'), function () {
                return new CompanyResource($this->company);
            }),
            'name' => $this->name,
            'status' => $this->status,
            'origin' => $this->origin,
            'details' => $this->details,
            "wcBrandId" => $this->wcBrandId,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'brand.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
