<?php

namespace App\Events\PurchaseProduct;

use App\Models\PurchaseProduct;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseProductCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PurchaseProduct
     */
    public $purchaseProduct;
    public $stockSerialIds;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PurchaseProduct $purchaseProduct, $productStockSerialIds = [])
    {
        $this->purchaseProduct = $purchaseProduct;

        $this->stockSerialIds = $productStockSerialIds;
    }
}
