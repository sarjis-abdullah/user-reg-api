<?php

namespace App\Events\Stock;

use App\Models\Stock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array
     */
    public $options;
    /**
     * @var Stock
     */
    public $stock;

    /**
     * Create a new event instance.
     *
     * @param Stock $stock
     * @param array $options
     */
    public function __construct(Stock $stock, array $options = [])
    {
        $this->stock = $stock;
        $this->options = $options;
    }
}
