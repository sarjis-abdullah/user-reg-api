<?php

namespace App\Mail\User;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetUserPassword extends Mailable
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;
    /**
     * @var PasswordReset
     */
    public $passwordReset;

    /**
     * Create a new message instance.
     *
     * @param PasswordReset $passwordReset
     * @param User $user
     */
    public function __construct(PasswordReset $passwordReset, User $user)
    {
        $this->passwordReset = $passwordReset;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $passwordReset = $this->passwordReset;
        $user = $this->user;

        return $this->subject("Reset your password.")->view('user.password-reset.index')
            ->with(['user' =>$user, 'pin' => $passwordReset->pin,]);
    }
}
