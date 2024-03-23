<?php

namespace App\Events\Product;

use App\Models\Product;

class ProductVariantsCreatedEvent
{
    /**
     * @var Product
     */
    public $product;
    /**
     * @var array
     */
    public $variations;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param array $variations
     */
    public function __construct(Product $product, array $variations)
    {
        $this->product = $product;
        $this->variations = $variations;
    }
}
