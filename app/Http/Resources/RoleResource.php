<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RoleResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'role.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),

            'title' => $this->title,
            'type' => $this->type,

            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'role.updatedByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
        ];
    }
}
