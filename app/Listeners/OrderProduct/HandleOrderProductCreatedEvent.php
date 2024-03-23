<?php

namespace App\Listeners\OrderProduct;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Models\ProductStockSerial;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\ProductStockSerialRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleOrderProductCreatedEvent implements ShouldQueue
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
     * @var ProductStockSerialRepository
     */
    protected $productStockSerialRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(StockRepository $stockRepository, StockLogRepository $stockLogRepository, ProductStockSerialRepository $productStockSerialRepository)
    {
        $this->stockRepository = $stockRepository;
        $this->stockLogRepository = $stockLogRepository;
        $this->productStockSerialRepository = $productStockSerialRepository;
    }

    /**
     * Handle the event.
     *
     * @param OrderProductCreatedEvent $event
     * @return void
     */
    public function handle(OrderProductCreatedEvent $event)
    {
        $orderProduct = $event->orderProduct;

        $stock = $this->stockRepository->findOne($orderProduct->stockId);

        $productStockSerial = $this->productStockSerialRepository->findOneBy(['productStockSerialId' => $event->productStockSerialId]);

        if ($productStockSerial instanceof ProductStockSerial){

            $this->productStockSerialRepository->update($productStockSerial, ['status' => ProductStockSerial::STATUS_SOLD_OUT]);
        }

        if($stock instanceof Stock) {
            $prevQuantity = $stock->quantity;
            $stockData['quantity'] = $stock->quantity - $orderProduct->quantity;
            if($stockData['quantity'] <= 0) {
                $stockData['status'] = Stock::STATUS_OUT_OF_STOCK;
            } else if($stockData['quantity'] <= $stock->alertQuantity) {
                $stockData['status'] = Stock::STATUS_LOW;
            }

            $stockData['stockProfit'] = $orderProduct->profitAmount + $stock->stockProfit;
            $stockData['discountAmount'] = $orderProduct->discount + $stock->discountAmount;
            $stockData['grossProfit'] = $stock->grossProfit + ($stockData['stockProfit'] - $stockData['discountAmount']);

            $updateStock = $this->stockRepository->update($stock, $stockData);

            $this->stockLogRepository->save([
                'stockId' => $stock->id,
                'productId' => $stock->productId,
                'resourceId' => $orderProduct->id,
                'type' => StockLog::TYPE_ORDER_PRODUCT,
                'prevQuantity' => $prevQuantity,
                'newQuantity' => $orderProduct->quantity,
                'quantity' => $updateStock->quantity,
                'profitAmount' => $orderProduct->profitAmount,
                'discountAmount' => $orderProduct->discount,
                'date' => $orderProduct->date,
            ]);
        }
    }
}
