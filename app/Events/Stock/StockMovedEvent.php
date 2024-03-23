<?php

namespace App\Events\Stock;

use App\Models\Stock;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockMovedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Stock
     */
    public $newStock;
    public $oldStock;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Stock $newStock, Stock $oldStock)
    {
        $this->newStock = $newStock;
        $this->oldStock = $oldStock;
    }
}
