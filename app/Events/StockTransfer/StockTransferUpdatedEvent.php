<?php

namespace App\Events\StockTransfer;

use App\Models\StockTransfer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockTransferUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var StockTransfer
     */
    public $stockTransfer;
    /**
     * @var string
     */
    public $status;

    /**
     * Create a new event instance.
     *
     * @param StockTransfer $stockTransfer
     * @param string $status
     */
    public function __construct(StockTransfer $stockTransfer, string $status)
    {
        $this->stockTransfer = $stockTransfer;
        $this->status = $status;
    }
}
