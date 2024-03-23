<?php

namespace App\Events\Product;

use App\Models\Product;
use Illuminate\Queue\SerializesModels;

class ProductUpdatedEvent
{
    use SerializesModels;

    /**
     * @var Product
     */
    public $product;
    /**
     * @var array
     */
    public $options;
    /**
     * @var string
     */
    public $barcodeType;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param string $barcodeType
     * @param array $options
     */
    public function __construct(Product $product, string $barcodeType, array $options = [])
    {
        $this->product = $product;
        $this->barcodeType = $barcodeType;
        $this->options = $options;
    }
}
