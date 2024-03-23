<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepository;
use Carbon\Carbon;

class EloquentNotificationRepository extends EloquentBaseRepository implements NotificationRepository
{
    /**
     * {@inheritDoc}
     */
    public function markAllReadStatus(bool $hasSeen)
    {
        $this->model->where('notifiable_id', $this->getLoggedInUser()->id)
            ->whereNull('read_at')
            ->get()
            ->each(fn ($notification) => $this->markReadStatus($notification, $hasSeen));
    }

    /**
     * {@inheritDoc}
     */
    public function markReadStatus(Notification $notification, bool $hasSeen)
    {
        $this->model->where('id', $notification->id)
            ->where('notifiable_id', $this->getLoggedInUser()->id)
            ->update(['read_at' => Carbon::now()]);
    }

    /**
     * {@inheritDoc}
     */
    public function countUnreadNotificationOfTheCurrentUser()
    {
        return $this->model->where('notifiable_id', $this->getLoggedInUser()->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        // only return auth user's notifications
        if (! isset($searchCriteria['notifiable_id'])) {
            $searchCriteria['notifiable_id'] = $this->getLoggedInUser()->id;
        }

        return parent::findBy($searchCriteria, $withTrashed);
    }
}
