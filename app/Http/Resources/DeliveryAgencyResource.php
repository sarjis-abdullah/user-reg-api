<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DeliveryAgencyResource extends Resource
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
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'contactPerson' => $this->contactPerson,
            'createdByUserId' => $this->createdByUserId,
            'updatedByUserId' => $this->updatedByUserId,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
