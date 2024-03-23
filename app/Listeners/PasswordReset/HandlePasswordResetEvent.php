<?php

namespace App\Listeners\PasswordReset;

use App\Events\PasswordReset\PasswordResetEvent;
use App\Mail\User\ResetUserPassword;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class HandlePasswordResetEvent implements ShouldQueue
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * HandleUserLoggedInEvent constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param PasswordResetEvent $event
     * @return void
     */
    public function handle(PasswordResetEvent $event)
    {
        $passwordReset = $event->passwordReset;
        $user = $passwordReset->user;

        //send email
        if($user->email) {
            Mail::to($user->email)->send(new ResetUserPassword($passwordReset, $user));
        }
    }
}
