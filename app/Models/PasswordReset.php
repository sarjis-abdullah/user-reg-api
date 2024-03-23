<?php

namespace App\Models;


use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PasswordReset extends Model
{
    use CommonModelFeatures;

    const TYPE_SET_PASSWORD_BY_PIN = 'verify';
    const TYPE_RESET_PASSWORD_BY_PIN = 'forgot_password';
    const TYPE_RESEND_PIN = 'resend_pin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId', 'pin', 'type', 'validTill'
    ];

    /**
     * get the user
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }
}
