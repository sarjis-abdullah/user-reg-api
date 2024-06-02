<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'birthDate' => $this->birthDate,
            'email' => $this->email,
            'occupation' => $this->occupation,
            'familyMembers' => $this->familyMembers,
            'gender' => $this->gender,
            'anniversary' => $this->anniversary,
            'hasComplimentaryCard' => $this->hasComplimentaryCard,
            'phoneVerified' => $this->phoneVerified,
            'emailVerified' => $this->emailVerified,
            'bloodGroup' => $this->bloodGroup,
            'member_id' => str_pad($this->member_id ?? 0, 8, '0', STR_PAD_LEFT),
            'lastName' => $this->lastName,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
