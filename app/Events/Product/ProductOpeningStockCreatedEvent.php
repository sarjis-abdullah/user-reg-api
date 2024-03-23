<?php

namespace App\Events\Product;

use App\Models\Product;

class ProductOpeningStockCreatedEvent
{

    /**
     * @var Product
     */
    public $product;
    /**
     * @var array
     */
    public $openingStock;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param array $openingStock
     */
    public function __construct(Product $product, array $openingStock)
    {
        $this->product = $product;
        $this->openingStock = $openingStock;
    }
}
