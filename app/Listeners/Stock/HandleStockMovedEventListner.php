<?php

namespace App\Listeners\Stock;

use App\Events\Stock\StockMovedEvent;
use App\Models\StockLog;
use App\Repositories\Contracts\StockLogRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleStockMovedEventListner
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
     * @return void
     */
    public function handle(StockMovedEvent $event)
    {
        $stock = $event->newStock;
        $oldStock = $event->oldStock;

        $stockLogRepository = app(StockLogRepository::class);

        $stockLogRepository->save([
            'stockId' => $stock->id,
            'productId' => $stock->productId,
            'resourceId' => $stock->id,
            'type' => StockLog::TYPE_STOCK_MOVED_TO_BUNDLE_PRODUCT,
            'prevQuantity' => $oldStock->quantity,
            'newQuantity' => $oldStock->quantity,
            'quantity' => $stock->quantity,
            'prevUnitCost' => $oldStock->unitCost,
            'newUnitCost' => $stock->unitCost,
            'prevUnitPrice' => $oldStock->unitPrice,
            'newUnitPrice' => $stock->unitPrice,
            'prevExpiredDate' => $oldStock->expiredDate,
            'newExpiredDate' => $stock->expiredDate,
            'date' => Carbon::now(),
        ]);
    }
}
