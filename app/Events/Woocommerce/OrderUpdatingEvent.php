<?php

namespace App\Events\Woocommerce;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdatingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Order
     */
    public $order;
    /**
     * @var array
     */
    public $options;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order, array $options = [])
    {
        $this->order = $order;
        $this->options = $options;
    }
}
