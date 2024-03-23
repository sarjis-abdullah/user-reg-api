<?php

namespace App\Events\OrderProduct;

use App\Models\OrderProduct;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderProductCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var OrderProduct
     */
    public $orderProduct;

    /*
     * @var Product Stock Serial Id
     * */
    public $productStockSerialId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OrderProduct $orderProduct, $productStockSerialId)
    {
        $this->orderProduct = $orderProduct;
        $this->productStockSerialId = $productStockSerialId;
    }
}
