<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DiscountResource extends Resource
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
            "id" => $this->id,
            "createdByUserId" => $this->createdByUserId,
            'createdByUser' => $this->when($this->needToInclude($request, 'tax.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            "title" => $this->title,
            "amount" => $this->amount,
            "type" => $this->type,
            "startDate" => $this->startDate,
            "endDate" => $this->endDate,
            "note" => $this->note,
            "updatedByUserId" => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'tax.createdByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
        ];
    }
}
