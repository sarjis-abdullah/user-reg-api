<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use CommonModelFeatures;

    const TYPE_SUPER_SHOP = 'super-shop';
    const TYPE_CLOTHING_SHOP = 'clothing-shop';

    const STATUS_ACTIVE = 'active';
    const STATUS_IRREGULAR = 'irregular';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'name',
        'address',
        'website',
        'email',
        'phone',
        'type',
        'details',
        'status',
        'updatedByUserId',
    ];

    /**
     * get the company branches
     *
     * @return HasMany
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'companyId', 'id');
    }

    /**
     * get the company managers
     *
     * @return HasMany
     */
    public function managers(): HasMany
    {
        return $this->hasMany(Manager::class, 'companyId', 'id');
    }

    /**
     * get the company employees
     *
     * @return HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'companyId', 'id');
    }

    /**
     * get the company customers
     *
     * @return HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'companyId', 'id');
    }

}
