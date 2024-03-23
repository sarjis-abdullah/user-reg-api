<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Order
     */
    public $order;
    /**
     * @var array
     */
    public $updatedOrderData;

    /**
     * Create a new event instance for order creation
     *
     * @param Order $order
     * @param array $updatedOrderData
     */
    public function __construct(Order $order, array $updatedOrderData = [])
    {
        $this->order = $order;
        $this->updatedOrderData = $updatedOrderData;
    }
}
