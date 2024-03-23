<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\StockSavingEvent;
use App\Models\Discount;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStockSavingEvent implements ShouldQueue
{
    use Queueable;

    protected $woocommerce;

    public function __construct(WoocomCommunicationService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    /**
     * @param StockSavingEvent $event
     * @return void
     */
    public function handle(StockSavingEvent $event)
    {
        $mode = $event->mode;
        $stock = $event->stock;
        $wcProductId = $event->wcProductId;
        $productData = $event->productData;

        if ($stock->productVariation) {
            $variationData = $this->prepareVariationData($stock, $wcProductId);

            if ($mode == 'saved') {
                $this->updateOrStoreVariation($wcProductId, $variationData, $stock);
            } elseif ($stock->wcStockId && $wcProductId) {
                $this->woocommerce->update("products/{$wcProductId}/variations/{$stock->wcStockId}", $variationData);
            }
        } else {
            $productData = $this->prepareProductData($stock);

            if ($mode == 'saved' && is_null($stock->wcStockId)) {
                $this->updateOrStoreProduct($productData, $stock);
            } elseif ($stock->wcStockId) {
                $this->woocommerce->update("products/{$stock->wcStockId}", $productData);
            }
        }
    }

    /**
     * @param $stock
     * @param $wcProductId
     * @return array
     */
    protected function prepareVariationData($stock, $wcProductId): array
    {
        return [
            'parent_id' => $wcProductId,
            'regular_price' => (string) $stock->unitPrice,
            'stock_quantity' => (int) $stock->quantity,
            'stock_status' => (int) $stock->quantity > 0 ? 'instock' : 'outofstock',
            'sku' => $stock->sku,
            'manage_stock' => true,
            'attributes' => $this->prepareAttributes($stock),
            'sale_price' => $this->calculateSalePrice($stock),
            'date_on_sale_from' => $this->calculateSalePrice($stock) ? $this->getSaleStartDate($stock) : null,
            'date_on_sale_to' => $this->calculateSalePrice($stock) ? $this->getSaleEndDate($stock) : null,
        ];
    }

    /**
     * @param $stock
     * @return array
     */
    protected function prepareAttributes($stock): array
    {
        return collect($stock->productVariation)
            ->only(['size', 'color', 'material'])
            ->filter()
            ->map(function ($attributeValue, $attributeName) {
                return [
                    'name' => ucfirst($attributeName),
                    'option' => $attributeValue,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * @param $wcProductId
     * @param $variationData
     * @param $stock
     * @return void
     */
    protected function updateOrStoreVariation($wcProductId, $variationData, $stock)
    {
        $wcVariation = $this->woocommerce->store("products/{$wcProductId}/variations", $variationData);

        $stock->update([
            'wcStockId' => $wcVariation->id,
            'ecomPublishedAt' => Carbon::now(),
            'permalink' => $wcVariation->permalink,
        ]);
    }

    /**
     * @param $stock
     * @return array
     */
    protected function prepareProductData($stock): array
    {
        return [
            'name' => $stock->product->name,
            'status' => 'publish',
            'description' => $stock->product->description,
            'short_description' => '',
            'sku' => $stock->sku,
            'regular_price' => (string) $stock->unitPrice,
            'sale_price' => $this->calculateSalePrice($stock),
            'date_on_sale_from' => $this->calculateSalePrice($stock) ? $this->getSaleStartDate($stock) : null,
            'date_on_sale_to' => $this->calculateSalePrice($stock) ? $this->getSaleEndDate($stock) : null,
            'stock_quantity' => (int) $stock->quantity,
            'stock_status' => (int) $stock->quantity > 0 ? 'instock' : 'outofstock',
            'manage_stock' => true,
            'type' => 'simple',
        ];
    }

    /**
     * @param $productData
     * @param $stock
     * @return void
     */
    protected function updateOrStoreProduct($productData, $stock)
    {
        $wcProduct = $this->woocommerce->store('products', $productData);
        $stock->product->update(['wcProductId' => 0]);

        $stock->update([
            'wcStockId' => $wcProduct->id,
            'ecomPublishedAt' => Carbon::now(),
            'permalink' => $wcProduct->permalink,
        ]);
    }

    /**
     * @param $stock
     * @return string|null
     */
    protected function calculateSalePrice($stock): ?string
    {
        if ($stock->product && $stock->product->isDiscountApplicable && $stock->product->discount) {
            $discount = $stock->product->discount;
            $discountedPrice = $discount->type == Discount::TYPE_PERCENTAGE
                ? $stock->unitPrice - ($stock->unitPrice * $discount->amount) / 100
                : $stock->unitPrice - $discount->amount;

            return (string) $discountedPrice;
        }

        return null;
    }

    protected function getSaleStartDate($stock)
    {
        return $stock->product && $stock->product->isDiscountApplicable && $stock->product->discount
            ? Carbon::parse($stock->product->discount->startDate)->toIso8601String()
            : null;
    }

    /**
     * @param $stock
     * @return string|null
     */
    protected function getSaleEndDate($stock)
    {
        return $stock->product && $stock->product->isDiscountApplicable && $stock->product->discount
            ? Carbon::parse($stock->product->discount->endDate)->toIso8601String()
            : null;
    }
}
