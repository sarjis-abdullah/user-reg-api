<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payroll extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'companyId',
        'branchId',
        'employeeId',
        'date',
        'account',
        'amount',
        'method',
        'reference',
        'updatedByUserId',
    ];

    /**
     * get the company
     *
     * @return HasOne
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'companyId');
    }

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
     * get the employee
     *
     * @return HasOne
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employeeId');
    }



    use HasFactory;
}
