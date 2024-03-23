<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class EcomIntegration extends Model
{
    use CommonModelFeatures;

    const NAME_WOOCOMMERCE = "woocommerce";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "createdByUserId",
        "branchId",
        "name",
        "apiUrl",
        "apiKey",
        "apiSecret",
        "updatedByUserId",
    ];

    /**
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

    /**
     * Set the apiKey attribute and encrypt it before saving.
     *
     * @param string $value
     * @return void
     */
    public function setApiKeyAttribute(string $value)
    {
        $this->attributes['apiKey'] = Crypt::encryptString($value);
    }

    /**
     * Set the apiSecret attribute and encrypt it before saving.
     *
     * @param string $value
     * @return void
     */
    public function setApiSecretAttribute(string $value)
    {
        $this->attributes['apiSecret'] = Crypt::encryptString($value);
    }

    /**
     * Get the apiKey attribute and decrypt it when fetching.
     *
     * @param string $value
     * @return string
     */
    public function getApiKeyAttribute(string $value): string
    {
        return Crypt::decryptString($value);
    }

    /**
     * Get the apiSecret attribute and decrypt it when fetching.
     *
     * @param string $value
     * @return string
     */
    public function getApiSecretAttribute(string $value): string
    {
        return Crypt::decryptString($value);
    }
}
