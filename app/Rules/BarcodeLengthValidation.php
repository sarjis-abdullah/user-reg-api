<?php

namespace App\Rules;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class BarcodeLengthValidation implements Rule
{
    /**
     * @var string
     */
    protected $barcodeType;
    /**
     * @var array
     */
    protected $messages;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $barcodeType)
    {
        $this->messages = [];
        $this->barcodeType = $barcodeType;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if(!in_array($this->barcodeType, explode(',', implode(',', Product::getConstantsByPrefix('BARCODE_TYPE_CODE_'))))) {
            $this->messages[] = ['barcode' => 'Invalid barcode type'];
            return false;
        } else if(in_array($this->barcodeType, [Product::BARCODE_TYPE_CODE_128, Product::BARCODE_TYPE_CODE_C39, Product::BARCODE_TYPE_CODE_C39P, Product::BARCODE_TYPE_CODE_PDF417])) {
            if(strlen($value) < 10 || strlen($value) > 10) {
                $this->messages[] = ['barcode' => 'barcode length should be 10 when barcode type is ' . $this->barcodeType];
                return false;
            }
        } else if(in_array($this->barcodeType, [Product::BARCODE_TYPE_CODE_EAN8, Product::BARCODE_TYPE_CODE_EAN13])) {
            if(strlen($value) < 4 || strlen($value) > 4) {
                $this->messages[] = ['barcode' => 'barcode length should be 4 when barcode type is ' . $this->barcodeType];
                return false;
            }
        } else if($this->barcodeType == Product::BARCODE_TYPE_CODE_EAN2) {
            if(strlen($value) < 8 || strlen($value) > 8) {
                $this->messages[] = ['barcode' => 'barcode length should be 8 when barcode type is ' . $this->barcodeType];
                return false;
            }
        } else if($this->barcodeType == Product::BARCODE_TYPE_CODE_EAN5) {
            if(strlen($value) < 7 || strlen($value) > 7) {
                $this->messages[] = ['barcode' => 'barcode length should be 7 when barcode type is ' . $this->barcodeType];
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message(): array
    {
        return $this->messages;
    }
}
