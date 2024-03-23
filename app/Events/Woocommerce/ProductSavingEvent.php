<?php

namespace App\Events\Woocommerce;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductSavingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Product
     */
    public $product;
    /**
     * @var Branch
     */
    public $branch;
    /**
     * @var string
     */
    public $mode;

    /**
     * Create a new event instance.
     *
     * @param string $mode
     * @param Product $product
     * @param Branch $branch
     */
    public function __construct(string $mode, Product $product, Branch $branch)
    {
        $this->mode = $mode;
        $this->product = $product;
        $this->branch = $branch;
    }
}
