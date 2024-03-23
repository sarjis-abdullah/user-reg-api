<?php

namespace App\Listeners\PurchaseProduct;

use App\Events\PurchaseProduct\PurchaseProductCreatedEvent;
use App\Models\ProductStockSerial;
use App\Models\Stock;
use App\Models\StockLog;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\ProductStockSerialRepository;
use App\Repositories\Contracts\PurchaseProductRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandlePurchaseProductCreatedEvent implements ShouldQueue
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
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var ProductStockSerialRepository
     */
    protected $productStockSerialRepository;

    /**
     * Create the event listener.
     *
     * @param StockRepository $stockRepository
     * @param StockLogRepository $stockLogRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(StockRepository $stockRepository, StockLogRepository $stockLogRepository, ProductRepository $productRepository, ProductStockSerialRepository $productStockSerialRepository)
    {
        $this->stockRepository = $stockRepository;
        $this->stockLogRepository = $stockLogRepository;
        $this->productRepository = $productRepository;
        $this->productStockSerialRepository = $productStockSerialRepository;
    }

    /**
     * Handle the event.
     *
     * @param PurchaseProductCreatedEvent $event
     * @return void
     */
    public function handle(PurchaseProductCreatedEvent $event)
    {
        $purchaseProduct = $event->purchaseProduct;
        $stockSerialIds  = $event->stockSerialIds;

        $stock = $this->stockRepository->findOneBy([
            'productId' => $purchaseProduct->productId,
            'branchId' => $purchaseProduct->branchId,
            'unitCost' => $purchaseProduct->unitCost,
            'unitPrice' => $purchaseProduct->sellingPrice,
            'expiredDate' => $purchaseProduct->expiredDate,
            'productVariationId' => $purchaseProduct->productVariationId,
        ]);

        if($stock instanceof Stock) {
            $prevQuantity = $stock->quantity;
            $stockData['quantity'] = $stock->quantity + $purchaseProduct->quantity;
            $stockData['discountAmount'] = $purchaseProduct->discountAmount;
            $stockData['discountedUnitCost'] = $purchaseProduct->discountedUnitCost;
            $stockData['discountType'] = $purchaseProduct->discountType;
            $stockData['existingUnitCost'] = $purchaseProduct->existingUnitCost;
            $stockData['existingDiscount'] = $purchaseProduct->existingDiscount;
            $stockData['purchaseQuantity'] = $purchaseProduct->purchaseQuantity;
            $updateStock = $this->stockRepository->update($stock, $stockData);

            $this->stockLogRepository->save([
                'createdByUserId' => $purchaseProduct->createdByUserId,
                'stockId' => $stock->id,
                'productId' => $stock->productId,
                'resourceId' => $purchaseProduct->id,
                'type' => StockLog::TYPE_PURCHASE_PRODUCT,
                'prevQuantity' => $prevQuantity,
                'newQuantity' => $purchaseProduct->quantity,
                'quantity' => $updateStock->quantity,
                'date' => $purchaseProduct->date,
                'expiredDate' => $purchaseProduct->expiredDate,
            ]);

            foreach ($stockSerialIds as $serialId){
                $this->productStockSerialRepository->save([
                    'createdByUserId' => $purchaseProduct->createdByUserId,
                    'stockId' => $stock->id,
                    'productId' => $stock->productId,
                    'productStockSerialId' => $serialId,
                    'status' => ProductStockSerial::STATUS_AVAILABLE
                ]);
            }

        } else {

            //TODO: not a good solution though back to this and fix
            $oldStock = $this->stockRepository->findOneBy([
                'productId' => $purchaseProduct->productId,
                'branchId' => $purchaseProduct->branchId,
                'sku' => $purchaseProduct->sku,
            ]);

            if($oldStock instanceof Stock) {
                $sku = $purchaseProduct->sku . '-' . $purchaseProduct->id;
                app(PurchaseProductRepository::class)->update($purchaseProduct, ['sku' => $sku]);
            } else {
                $sku = $purchaseProduct->sku;
            }

            $newStock = $this->stockRepository->save([
                'createdByUserId' => $purchaseProduct->createdByUserId,
                'productId' => $purchaseProduct->productId,
                'productVariationId' => $purchaseProduct->productVariationId,
                'branchId' => $purchaseProduct->branchId,
                'quantity' => $purchaseProduct->quantity,
                'expiredDate' => $purchaseProduct->expiredDate,
                'unitCost' => $purchaseProduct->unitCost,
                'unitPrice' => $purchaseProduct->sellingPrice,
                'sku' => $sku,
                'status' => Stock::STATUS_AVAILABLE,
                'alertQuantity' => 100, //TODO set default in modal
                'discountAmount' => $purchaseProduct->discountAmount,
                'discountedUnitCost' => $purchaseProduct->discountedUnitCost,
                'discountType' => $purchaseProduct->discountType,
                'existingUnitCost' => $purchaseProduct->existingUnitCost,
                'existingDiscount' => $purchaseProduct->existingDiscount,
                'tax' => $purchaseProduct->tax,
                'purchaseQuantity' => $purchaseProduct->purchaseQuantity,
            ]);

            foreach ($stockSerialIds as $serialId){
                $this->productStockSerialRepository->save([
                    'stockId' => $newStock->id,
                    'productId' => $newStock->productId,
                    'productStockSerialId' => $serialId,
                    'status' => ProductStockSerial::STATUS_AVAILABLE
                ]);
            }

            $this->stockLogRepository->save([
                'createdByUserId' => $purchaseProduct->createdByUserId,
                'stockId' => $newStock->id,
                'productId' => $newStock->productId,
                'resourceId' => $purchaseProduct->id,
                'type' => StockLog::TYPE_PURCHASE_PRODUCT,
                'prevQuantity' => 0,
                'newQuantity' => $purchaseProduct->quantity,
                'quantity' => $newStock->quantity,
                'date' => $purchaseProduct->date,
            ]);
        }

        $product = $this->productRepository->findOne($purchaseProduct->productId);

        if ($product){
            $this->productRepository->update($product, ['status' => Stock::STATUS_AVAILABLE]);
        }
    }
}
