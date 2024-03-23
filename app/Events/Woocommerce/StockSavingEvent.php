<?php

namespace App\Events\Woocommerce;

use App\Models\Stock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockSavingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $mode;
    /**
     * @var Stock
     */
    public $stock;
    /**
     * @var null
     */
    public $wcProductId;
    /**
     * @var array
     */
    public $productData;

    /**
     * Create a new event instance.
     *
     * @param string $mode
     * @param Stock $stock
     * @param mixed $wcProductId
     * @param array $productData
     */
    public function __construct(string $mode, Stock $stock, $wcProductId = null, array $productData = [])
    {
        $this->mode = $mode;
        $this->stock = $stock;
        $this->wcProductId = $wcProductId;
        $this->productData = $productData;
    }
}
