<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CompanyResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'company.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'name' => $this->name,
            'address' => $this->address,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'details' => $this->details,
            'status' => $this->status,
            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'company.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
