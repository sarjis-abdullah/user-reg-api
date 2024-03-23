<?php

namespace App\Repositories\Contracts;

use App\Models\Notification;

interface NotificationRepository extends BaseRepository
{
    /**
     * * Set all of a user's notification seen status
     *
     * @param bool $hasSeen
     * @return mixed
     */
    public function markAllReadStatus(bool $hasSeen);

    /**
     * Set user's notification seen status
     *
     * @param Notification $notification
     * @param bool $hasSeen
     * @return mixed
     */
    public function markReadStatus(Notification $notification, bool $hasSeen);

    /**
     * count unread notification of a user
     *
     * @return mixed
     */
    public function countUnreadNotificationOfTheCurrentUser();

    /**
     * Search All notifications, default return only unseen
     *
     * @param  array  $searchCriteria
     * @param  bool  $withTrashed
     * @return mixed
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false);
}
