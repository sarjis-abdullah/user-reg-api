<?php

namespace App\Listeners\Stock;

use App\Events\Stock\StockUpdatedEvent;
use App\Listeners\CommonListenerFeatures;
use App\Models\StockLog;
use App\Notifications\StockAlert;
use App\Repositories\Contracts\StockLogRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStockUpdatedEvent implements ShouldQueue
{
    use CommonListenerFeatures;

    /**
     * Handle the event.
     *
     * @param StockUpdatedEvent $event
     * @return void
     */
    public function handle(StockUpdatedEvent $event)
    {
        $stock = $event->stock;
        $eventOptions = $event->options;
        $oldStockItem = $eventOptions['oldModel'];

        $hasUnitPriceChanged = $this->hasAFieldValueChanged($stock, $oldStockItem, 'unitPrice');
        $hasUnitCostChanged = $this->hasAFieldValueChanged($stock, $oldStockItem, 'unitCost');
        $hasExpiredDateChanged = $this->hasAFieldValueChanged($stock, $oldStockItem, 'expiredDate');

        if($hasUnitPriceChanged || $hasUnitCostChanged || $hasExpiredDateChanged) {
            $stockLogRepository = app(StockLogRepository::class);

            $stockLogRepository->save([
                'stockId' => $stock->id,
                'productId' => $stock->productId,
                'resourceId' => $stock->id,
                'type' => $hasUnitPriceChanged ? StockLog::TYPE_UNIT_PRICE_CHANGED : StockLog::TYPE_UNIT_COST_CHANGED,
                'prevQuantity' => $oldStockItem->quantity,
                'newQuantity' => 0,
                'quantity' => $stock->quantity,
                'prevUnitCost' => $oldStockItem->unitCost,
                'newUnitCost' => $stock->unitCost,
                'prevUnitPrice' => $oldStockItem->unitPrice,
                'newUnitPrice' => $stock->unitPrice,
                'prevExpiredDate' => $oldStockItem->expiredDate,
                'newExpiredDate' => $stock->expiredDate,
                'date' => Carbon::now(),
            ]);
        }else{

            $stockLogRepository = app(StockLogRepository::class);

            $stockLogRepository->save([
                'stockId' => $stock->id,
                'productId' => $stock->productId,
                'resourceId' => $stock->id,
                'type' => StockLog::TYPE_RESTOCK_FROM_BUNDLE_PRODUCT,
                'prevQuantity' => $oldStockItem->quantity,
                'newQuantity' => 0,
                'quantity' => $stock->quantity,
                'prevUnitCost' => $oldStockItem->unitCost,
                'newUnitCost' => $stock->unitCost,
                'prevUnitPrice' => $oldStockItem->unitPrice,
                'newUnitPrice' => $stock->unitPrice,
                'prevExpiredDate' => $oldStockItem->expiredDate,
                'newExpiredDate' => $stock->expiredDate,
                'date' => Carbon::now(),
            ]);
        }

        //hided for now, though we are sending stock alert report on schedule job. this code creating dealy on response
//        if((float) $stock->quantity <= (float) $stock->product->alertQuantity) {
//            $users = $stock->branch->adminUserRoles->map(fn ($userRole) => $userRole->user);
//
//            $users->each(function ($user) use ($stock) {
//                $user->notify(new StockAlert($stock));
//            });
//        }

    }
}
