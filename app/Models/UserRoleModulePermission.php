<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserRoleModulePermission extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'branchId',
        'userId',
        'roleId',
        'moduleActionIds',
        'moduleActionNames',
        'updatedByUserId',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'moduleActionIds' => 'array',
        'moduleActionNames' => 'array'
    ];

    /**
     * get the branch
     *
     * @return HasOne
     */
    public function branch(): HasOne
    {
        return $this->hasOne(Branch::class, 'id', 'branchId');
    }

    /**
     * get the user
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userId');
    }

    /**
     * get the role
     *
     * @return HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'roleId');
    }

    /**
     * get the module actions
     **/
    public function moduleActions()
    {
        //TODO: not an idol solution
//        $userRepository = app(UserRepository::class);
//
//        return $userRepository->findBy(['id' => implode(',', $this->requestedToUserIds)]);

        return ModuleAction::whereIn('id', $this->moduleActionIds)->get();
    }
}
