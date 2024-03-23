<?php

namespace App\Http\Resources;

use App\Repositories\Contracts\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'totalUnreadMessages' => app(NotificationRepository::class)->countUnreadNotificationOfTheCurrentUser(),
            ],
        ];
    }
}
