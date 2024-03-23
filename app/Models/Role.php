<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'type',
        'title'
    ];

    // N.B. setting `id` statically for quicker insert where called
    const ROLE_ADMIN_SUPER = ['id' => 1, 'type' => 'admin', 'title' => 'super_admin'];
    const ROLE_ADMIN_STANDARD = ['id' => 2, 'type' => 'admin', 'title' => 'standard_admin'];
    const ROLE_ADMIN_LIMITED = ['id' => 3, 'type' => 'admin', 'title' => 'limited_admin'];

    const ROLE_MANAGER_SUPER = ['id' => 4, 'type' => 'manager', 'title' => 'super_manager'];
    const ROLE_MANAGER_STANDARD = ['id' => 5, 'type' => 'manager', 'title' => 'standard_manager'];
    const ROLE_MANAGER_RESTRICTED = ['id' => 6, 'type' => 'manager', 'title' => 'restricted_manager'];

    const ROLE_EMPLOYEE_BASIC = ['id' => 7, 'type' => 'employee', 'title' => 'basic_employee'];


    /**
     * @param $title
     * @return void
     */
    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = strtolower(str_replace(' ', '_', $title));
    }

    /**
     * is a super admin role
     *
     * @return bool
     */
    public function isSuperAdminRole(): bool
    {
        return $this->title === self::ROLE_ADMIN_SUPER['title'];
    }

    /**
     * is a standard admin role
     *
     * @return bool
     */
    public function isStandardAdminRole(): bool
    {
        return $this->title === self::ROLE_ADMIN_STANDARD['title'];
    }

    /**
     * is a limited admin role
     *
     * @return bool
     */
    public function isLimitedAdminRole(): bool
    {
        return $this->title === self::ROLE_ADMIN_LIMITED['title'];
    }

    /**
     * has any admin role
     *
     * @return bool
     */
    public function hasAdminRole(): bool
    {
        return in_array($this->title, [self::ROLE_ADMIN_SUPER['title'], self::ROLE_ADMIN_STANDARD['title'], self::ROLE_ADMIN_LIMITED['title']]);
    }

    /**
     * is a super manager role
     *
     * @return bool
     */
    public function isSuperManagerRole(): bool
    {
        return $this->title === self::ROLE_MANAGER_SUPER['title'];
    }

    /**
     * is a standard manager role
     *
     * @return bool
     */
    public function isStandardManagerRole(): bool
    {
        return $this->title === self::ROLE_MANAGER_STANDARD['title'];
    }


    /**
     * has any manager role
     *
     * @return bool
     */
    public function hasManagerRole(): bool
    {
        return in_array($this->title, [self::ROLE_MANAGER_SUPER['title'], self::ROLE_MANAGER_STANDARD['title']]);
    }

}
