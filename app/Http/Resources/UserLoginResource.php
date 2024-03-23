<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserLoginResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'locale' => $this->locale,
            'isActive' => $this->isActive,
            'userRoles' => $this->when($this->needToInclude($request, 'ul.userRoles'), function () {
                return UserRoleResource::collection($this->userRoles);
            }),
            'userRole' => $this->when($this->needToInclude($request, 'ul.userRole'), function () {
                return new UserRoleResource($this->userRole);
            }),
            'lastLoginAt' => $this->lastLoginAt,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
