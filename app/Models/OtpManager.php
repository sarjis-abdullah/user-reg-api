<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'expireAt',
        'type',
        'code',
    ];
}
