<?php

namespace App\Listeners\PurchaseProductReturn;

use App\Events\PurchaseProductReturn\PurchaseProductReturnCreatedEvent;
use App\Models\PurchaseProduct;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\PurchaseProductRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandlePurchaseProductReturnCreatedEvent implements ShouldQueue
{
    /**
     * @var PurchaseProductRepository
     */
    protected $purchaseProductRepository;
    /**
     * @var StockRepository
     */
    protected $stockRepository;
    /**
     * @var StockLogRepository
     */
    protected $stockLogRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PurchaseProductRepository $purchaseProductRepository, StockRepository $stockRepository, StockLogRepository $stockLogRepository)
    {
        $this->purchaseProductRepository = $purchaseProductRepository;
        $this->stockRepository = $stockRepository;
        $this->stockLogRepository = $stockLogRepository;
    }


    /**
     * Handle the event.
     *
     * @param PurchaseProductReturnCreatedEvent $event
     * @return void
     */
    public function handle(PurchaseProductReturnCreatedEvent $event)
    {
        $purchaseProductReturn = $event->purchaseProductReturn;

        $purchaseProductRepo = app(PurchaseProductRepository::class);

        $purchaseProduct = $purchaseProductReturn->purchaseProduct;

        if ($purchaseProduct instanceof PurchaseProduct){
            $purchaseProductRepo->update($purchaseProduct, [
                'returnQuantity' => $purchaseProduct->returnQuantity + $purchaseProductReturn->quantity,
                'returnTotalAmount' => $purchaseProduct->returnTotalAmount + $purchaseProductReturn->returnAmount,
            ]);
        }

        $stock = $this->stockRepository->findOneBy(['productId' => $purchaseProduct->productId, 'branchId' => $purchaseProduct->branchId, 'sku' => $purchaseProduct->sku]);

        if($stock instanceof Stock) {
            $prevQuantity = $stock->quantity;
            $stockData['quantity'] = $stock->quantity - $purchaseProductReturn->quantity;

            if($stockData['quantity'] <= 0) {
                $stockData['status'] = Stock::STATUS_OUT_OF_STOCK;
            } else if($stockData['quantity'] <= $stock->alertQuantity) {
                $stockData['status'] = Stock::STATUS_LOW;
            }

            $updateStock = $this->stockRepository->update($stock, $stockData);

            $this->stockLogRepository->save([
                'stockId' => $stock->id,
                'productId' => $purchaseProduct->productId,
                'resourceId' => $purchaseProductReturn->id,
                'type' => StockLog::TYPE_PURCHASE_PRODUCT_RETURN,
                'prevQuantity' => $prevQuantity,
                'newQuantity' => $purchaseProductReturn->quantity,
                'quantity' => $updateStock->quantity,
                'date' => $purchaseProductReturn->date,
            ]);
        }
    }
}
