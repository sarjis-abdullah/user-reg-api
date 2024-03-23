<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Storage;

class EmailNotificationSentListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  NotificationSent  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        // Check if the notification was sent via email
        if ($event->channel === 'mail') {
            // Add your file deletion logic here
            if (isset($event->notification->file_name) && Storage::disk('public')->exists($event->notification->directory_name . '/' .$event->notification->file_name)) {
                Storage::disk('public')->delete($event->notification->directory_name . '/' .$event->notification->file_name);
            }
        }
    }
}
