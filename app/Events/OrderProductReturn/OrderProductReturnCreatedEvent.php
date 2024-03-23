<?php

namespace App\Events\OrderProductReturn;

use App\Models\OrderProductReturn;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderProductReturnCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var OrderProductReturn
     */
    public $orderProductReturn;

    /**
     * Create a new event instance.
     *
     * @param OrderProductReturn $orderProductReturn
     */
    public function __construct(OrderProductReturn $orderProductReturn)
    {
        $this->orderProductReturn = $orderProductReturn;
    }
}
