<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AdminResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'admin.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'userId' => $this->userId,
            'user' => $this->when($this->needToInclude($request, 'admin.user'), function () {
                return new UserResource($this->user);
            }),

            'userRoleId' => $this->userRoleId,
            'userRole' => $this->when($this->needToInclude($request, 'admin.userRole'), function () {
                return new UserRoleResource($this->userRole);
            }),

            'level' => $this->level,

            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'admin.updatedByUser'), function () {
                return new UserResource($this->createdByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
