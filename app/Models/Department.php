<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use CommonModelFeatures;

    protected $fillable = [
        'name', 'createdByUserId', 'updatedByUserId'
    ];

    /**
     * @return HasMany
     */
    public function subDepartments(): HasMany
    {
        return $this->hasMany(SubDepartment::class, 'department_id', 'id');
    }
}
