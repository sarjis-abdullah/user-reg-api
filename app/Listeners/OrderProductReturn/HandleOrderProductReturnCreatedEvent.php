<?php

namespace App\Listeners\OrderProductReturn;

use App\Events\OrderProductReturn\OrderProductReturnCreatedEvent;
use App\Models\OrderProduct;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderProductReturnRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleOrderProductReturnCreatedEvent implements ShouldQueue
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
     * @var OrderProductRepository
     */
    protected $orderProductRepository;
    /**
     * @var OrderProductReturnRepository
     */
    protected $orderProductReturnRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OrderProductReturnRepository $orderProductReturnRepository, OrderProductRepository $orderProductRepository, StockRepository $stockRepository, StockLogRepository $stockLogRepository)
    {
        $this->orderProductReturnRepository = $orderProductReturnRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->stockRepository = $stockRepository;
        $this->stockLogRepository = $stockLogRepository;
    }

    /**
     * Handle the event.
     *
     * @param OrderProductReturnCreatedEvent $event
     * @return void
     */
    public function handle(OrderProductReturnCreatedEvent $event)
    {
        $orderProductReturn = $event->orderProductReturn;

        $orderProduct = $orderProductReturn->orderProduct;

        $status = $orderProduct->quantity == $orderProductReturn->quantity ? OrderProduct::STATUS_RETURNED : OrderProduct::STATUS_PARTIAL_RETURNED;

        $this->orderProductRepository->update($orderProduct, ['status' => $status]);

        $stock = $orderProduct->stock;

        $profitAmount = $stock->unitProfit * $orderProductReturn->quantity;
        $discountAmount = ($orderProduct->unitPrice - $orderProduct->discountedUnitPrice) * $orderProductReturn->quantity;

        $this->orderProductReturnRepository->update($orderProductReturn, ['profitAmount' => $profitAmount, 'discountAmount' => $discountAmount]);

        if($stock instanceof Stock) {
            $prevQuantity = $stock->quantity;
            $stockData['quantity'] = $stock->quantity + $orderProductReturn->quantity;

            $updateStock = $this->stockRepository->update($stock, $stockData);

            $this->stockLogRepository->save([
                'stockId' => $stock->id,
                'productId' => $orderProduct->productId,
                'resourceId' => $orderProductReturn->id,
                'type' => StockLog::TYPE_ORDER_PRODUCT_RETURN,
                'prevQuantity' => $prevQuantity,
                'newQuantity' => $orderProductReturn->quantity,
                'quantity' => $updateStock->quantity,
                'profitAmount' => $profitAmount,
                'discountAmount' => $discountAmount,
                'date' => $orderProductReturn->date ?? now(),
            ]);
        }

    }
}
