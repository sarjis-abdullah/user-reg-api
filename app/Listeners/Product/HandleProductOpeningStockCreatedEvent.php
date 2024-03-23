<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductOpeningStockCreatedEvent;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleProductOpeningStockCreatedEvent implements ShouldQueue
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
     * @param ProductOpeningStockCreatedEvent $event
     * @return void
     */
    public function handle(ProductOpeningStockCreatedEvent $event)
    {
        $openingStock = $event->openingStock;
        $product = $event->product;

        $openingStock['sku'] = Purchase::generateSku($product->name, $product->id, $openingStock['unitPrice']);
        $openingStock['productId'] = $product->id;
        $openingStock['status'] = Stock::STATUS_AVAILABLE;
        $openingStock['alertQuantity'] = 100;
        $openingStock['createdByUserId'] = $product->createdByUserId;

        $stock = $this->stockRepository->save($openingStock);

        $stockLogRepo = app(StockLogRepository::class);

        $stockLogRepo->save([
            'stockId' => $stock->id,
            'productId' => $product->id,
            'type' => StockLog::TYPE_OPENING_STOCK_TO_BRANCH,
            'prevQuantity' => 0,
            'newQuantity' => $stock->quantity,
            'quantity' => $stock->quantity,
            'createdByUserId' => $product->createdByUserId,
            'date' => Carbon::now(),
        ]);
    }
}
