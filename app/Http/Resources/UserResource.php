<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends Resource
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
            'userRole' => $this->when($this->needToInclude($request, 'user.userRole'), function () {
                return new UserRoleResource($this->userRole);
            }),
            'userRoles' => $this->when($this->needToInclude($request, 'user.userRoles'), function () {
                return UserRoleResource::collection($this->userRoles);
            }),
            'userProfile' => $this->when($this->needToInclude($request, 'user.userProfile'), function () {
                return new UserProfileResource($this->userProfile);
            }),
            'lastLoginAt' => $this->lastLoginAt,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pref_notification_type' => $this->pref_notification_type,
            'pref_notification_time' => $this->pref_notification_time
        ];
    }
}
