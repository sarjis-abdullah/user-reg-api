<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expense extends Model
{
    use CommonModelFeatures;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'categoryId',
        'branchId',
        'amount',
        'expenseReason',
        'expenseDate',
        'paymentType',
        'notes',
        'updatedByUserId',
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
     * get the category
     *
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(ExpenseCategory::class, 'id', 'categoryId');
    }
}
