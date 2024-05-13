<?php

namespace App\Listeners;

use App\Mail\SendUserRegistrationComfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class UserRegistrationCompletedListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        Mail::to($user)
            ->queue(new SendUserRegistrationComfirmationMail($user));

    }
}
