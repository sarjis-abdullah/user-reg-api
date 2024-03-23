<?php

namespace App\Events\PasswordReset;

use App\Models\PasswordReset;
use Illuminate\Queue\SerializesModels;

class PasswordResetEvent
{
    use SerializesModels;

    /**
     * @var PasswordReset
     */
    public PasswordReset $passwordReset;
    public array $options;

    /**
     * Create a new event instance.
     *
     * @param PasswordReset $passwordReset
     * @param array $options
     * @return void
     */
    public function __construct(PasswordReset $passwordReset, array $options = [])
    {
        $this->passwordReset = $passwordReset;
        $this->options = $options;
    }
}
