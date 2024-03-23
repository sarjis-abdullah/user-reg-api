<?php

namespace App\Rules;

use App\Models\StockTransfer;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ValidateStockTransferStatus implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $statuses = explode(',', $value);
        foreach ($statuses as $status){
            if (!in_array($status, StockTransfer::STATUS_LIST)){
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has invalid value';
    }
}
