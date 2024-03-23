<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use App\Models\Traits\Users\AdminUserMethods;
use App\Models\Traits\Users\CommonUserMethods;
use App\Models\Traits\Users\ManagerUserMethods;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use CommonModelFeatures, Notifiable, HasApiTokens;
    use AdminUserMethods, ManagerUserMethods, CommonUserMethods;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'locale',
        'isActive',
        'lastLoginAt',
        'pref_notification_type',
        'pref_notification_time',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * set default values
     *
     * @var array
     */
    protected $attributes = [
        'isActive' => 0
    ];

    /**
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * get label
     *
     * @param $name
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = ucfirst($name);
    }


    /**
     * is a active user
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (boolean) $this->isActive;
    }

    /**
     * get user's profile profile info
     *
     * @return HasOne
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'userId', 'id');
    }

    /**
     * user and roles relationship
     *
     * @return HasOne
     */
    public function userRole(): HasOne
    {
        return $this->hasOne(UserRole::class, 'userId', 'id')->orderByDesc('roleId');
    }

    /**
     * user and roles relationship
     *
     * @return HasMany
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'userId', 'id');
    }

    /**
     * get all roles titles of a user
     *
     * @return array - of strings
     *
     */
    public function getRolesTitles(): array
    {
        $roles = [];
        foreach ($this->userRoles as $userRole) {
            $roles[] = $userRole->role->title;
        }
        return $roles;
    }
}
