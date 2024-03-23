<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->data['subject'],
            'text' => $this->data['app_description'],
            'user_id' => $this->notifiable_id,
            'reference_type_id' => $this->data['reference_type_id'],
            'reference_key' => $this->data['reference_key'],
            'click_url' => $this->data['link'],
            'seen' => ! is_null($this->read_at),
            'created_at' => $this->created_at,
        ];
    }
}
