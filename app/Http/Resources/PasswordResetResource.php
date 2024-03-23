<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PasswordResetResource extends Resource
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
            'email' => $this->email,
            'phone' => $this->phone
        ];
    }
}
