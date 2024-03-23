<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserProfileResource extends Resource
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
            'createdByUser' => $this->when($this->needToInclude($request, 'up.createdByUser'), function () {
                return new UserResource($this->createdByUser);
            }),

            'userId' => $this->userId,
            'gender' => $this->gender,
            'occupation' => $this->occupation,
            'address' => $this->address,
            'homeTown' => $this->homeTown,
            'birthDate' => $this->birthDate,
            'interests' => $this->interests,

            'updatedByUserId' => $this->updatedByUserId,
            'updatedByUser' => $this->when($this->needToInclude($request, 'up.updatedByUser'), function () {
                return new UserResource($this->updatedByUser);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
