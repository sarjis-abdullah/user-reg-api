<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends IlluminateDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @return Model
     */
    public function send($notifiable, Notification $notification): Model
    {
        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification->id,
            'type' => get_class($notification),
            'data' => $this->getData($notifiable, $notification),
            'read_at' => self::isMailOnlyNotification($notification) ? Carbon::now() : null,
            'notified_at' => self::isMailOnlyNotification($notification)
                || self::isAppOnlyNotification($notification)
                || $notifiable->pref_notification_type != 'scheduled' ? Carbon::now() : null,
        ]);
    }

    private static function isAppOnlyNotification($notification): bool
    {
        return in_array(get_class($notification), []);
    }

    private static function isMailOnlyNotification($notification): bool
    {
        return in_array(get_class($notification), []);
    }
}
