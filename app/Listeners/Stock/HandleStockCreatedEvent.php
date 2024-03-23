<?php

namespace App\Listeners\Stock;

use App\Events\Stock\StockCreatedEvent;
use App\Models\Branch;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStockCreatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param StockCreatedEvent $event
     * @return void
     */
    public function handle(StockCreatedEvent $event)
    {
        $stock = $event->stock;

        if($stock->branch->type == Branch::TYPE_ECOMMERCE) {
            if($stock->ecomProductId) {
                //product already in the ecom site. update stock
            } else {
                //product is new create a new product in ecomm site as well
                $ecomProduct = [
                    'name' => $stock->product->name,
                    'type' => 'simple'
                ];

            }
        }
    }
}
