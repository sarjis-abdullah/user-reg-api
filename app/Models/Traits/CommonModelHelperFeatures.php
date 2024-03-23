<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CommonModelHelperFeatures
{
    /**
     * Get the user who created
     *
     * @return BelongsTo
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'createdByUserId');
    }

    /**
     * static method for getting table name
     *
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * get constants of a model by prefix
     *
     * @return array
     */
    public static function getConstantsByPrefix($prefix)
    {
        $reflectionClass = new \ReflectionClass(self::class);

        $constants = array_filter($reflectionClass->getConstants(), function ($constant) use ($prefix) {
            return strpos($constant, $prefix) === 0;
        }, ARRAY_FILTER_USE_KEY);

        return array_values($constants);
    }

    /**
     * get the updatedByUser
     *
     * @return BelongsTo
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updatedByUserId');
    }

}
