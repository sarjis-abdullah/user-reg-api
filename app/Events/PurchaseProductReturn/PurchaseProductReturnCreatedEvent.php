<?php

namespace App\Events\PurchaseProductReturn;

use App\Models\PurchaseProductReturn;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseProductReturnCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PurchaseProductReturn
     */
    public $purchaseProductReturn;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PurchaseProductReturn $purchaseProductReturn)
    {
        $this->purchaseProductReturn = $purchaseProductReturn;
    }

}
