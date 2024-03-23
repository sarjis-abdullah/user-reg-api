<?php

namespace App\Events\StockTransfer;

use App\Models\StockTransfer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockTransferCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var StockTransfer
     */
    public $stockTransfer;

    /**
     * Create a new event instance.
     *
     * @param StockTransfer $stockTransfer
     */
    public function __construct(StockTransfer $stockTransfer)
    {
        $this->stockTransfer = $stockTransfer;
    }
}
