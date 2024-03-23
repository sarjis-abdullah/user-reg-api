<?php

namespace App\Listeners\Adjustment;

use App\Events\Adjustment\AdjustmentCreatedEvent;
use App\Models\Adjustment;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleAdjustmentCreatedEvent implements ShouldQueue
{
    /**
     * @var StockLogRepository
     */
    protected $stockLogRepository;

    /**
     * @var StockRepository
     */
    protected $stockRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(StockRepository $stockRepository, StockLogRepository $stockLogRepository)
    {
        $this->stockRepository = $stockRepository;
        $this->stockLogRepository = $stockLogRepository;
    }

    /**
     * Handle the event.
     *
     * @param AdjustmentCreatedEvent $event
     * @return void
     */
    public function handle(AdjustmentCreatedEvent $event)
    {
        $adjustment = $event->adjustment;

        //update stock on increment
        $stock = $this->stockRepository->findOne($adjustment->stockId);

        if($stock instanceof Stock) {
            $prevQuantity = $stock->quantity;

            if($adjustment->type == Adjustment::TYPE_INCREMENT) {
                $stockData['quantity'] = $stock->quantity + $adjustment->quantity; //stock update on increment
                $updateStock = $this->stockRepository->update($stock, $stockData);
                $type = StockLog::TYPE_ADJUSTMENT_PRODUCT_INCREMENT;
            } else {
                $stockData['quantity'] = $stock->quantity - $adjustment->quantity; //stock update on deincrement
                $updateStock = $this->stockRepository->update($stock, $stockData);
                $type = StockLog::TYPE_ADJUSTMENT_PRODUCT_DECREMENT;
            }

            $this->stockLogRepository->save([
                'stockId' => $stock->id,
                'productId' => $stock->productId,
                'resourceId' => $adjustment->id,
                'type' => $type,
//                'prevQuantity' => $stock->quantity, //previous code
                'prevQuantity' => $prevQuantity, //updated code
                'newQuantity' => $adjustment->quantity,
                'quantity' => $updateStock->quantity,
                'date' => $adjustment->date,
            ]);
        }
    }
}
