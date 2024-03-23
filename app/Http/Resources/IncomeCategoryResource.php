<?php

namespace App\Http\Resources;

class IncomeCategoryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            "updatedByUserId" => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'incomeCategory.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            "createdByUserId" => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'incomeCategory.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
        ];
    }
}
