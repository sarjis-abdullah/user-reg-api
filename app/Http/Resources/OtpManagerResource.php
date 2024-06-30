<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OtpManagerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'phone' => $this->user->phone,
        ];
    }
}
