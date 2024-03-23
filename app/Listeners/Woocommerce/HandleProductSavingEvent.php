<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\ProductSavingEvent;
use App\Events\Woocommerce\StockSavingEvent;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class HandleProductSavingEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @var WoocomCommunicationService
     */
    protected $woocommerce;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(WoocomCommunicationService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    /**
     * Handle the event.
     *
     * @param ProductSavingEvent $event
     * @throws BindingResolutionException
     */
    public function handle(ProductSavingEvent $event)
    {
        $mode = $event->mode;
        $branch = $event->branch;
        $product = $event->product;
        $productVariations = $product->productVariations;
        $stocks = $product->stocks->where('branchId', $branch->id);

        // Prepare the product data
        $productData = [
            'name' => $product->name,
            'status' => 'publish',
            'description' => $product->description,
            'short_description' => ''
        ];

        if($product->category && $product->category->wcCategoryId) {
            $productData['categories'][] =  ['id' => $product->category->wcCategoryId];
        }
        if($product->subCategory  && $product->subCategory->wcSubCategoryId) {
            $productData['categories'][] = ['id' => $product->subCategory->wcSubCategoryId];
        }
        if($product->brand && $product->brand->wcBrandId) {
            $productData['brands'] = [
                ['id' => $product->brand->wcBrandId]
            ];
        }
        if($product->tax && $product->tax->wcTaxId) {
            $productData['tax_status'] = 'taxable';
            $productData['tax_class'] = 'standard';
            $productData['taxes'] = [
                ['id' => $product->tax->wcTaxId, 'rate' => $product->tax->amount]
            ];
        }

        if(count($productVariations)) {
            $variations = collect($productVariations)
                ->filter(function ($productVariation) use ($stocks) {
                    // Check if there are stocks with quantity greater than 0 for the current variation
                    return $stocks->where('productVariationId', $productVariation->id)
                        ->where('quantity', '>', 0)->isNotEmpty();
                })->map(function ($productVariation) {
                    // Your mapping logic goes here
                    // This function will only be called for variations with stocks quantity > 0
                    return $productVariation;
                });

            $attributes = collect($variations)
                ->map(function ($variation) {
                return collect($variation)
                    ->only(['size', 'color', 'material'])
                    ->reject(function ($value) {
                        return $value === null;
                    })
                    ->map(function ($attributeValue, $attributeName) {
                        $attributeName = ucfirst($attributeName); // Uppercase the first letter

                        return [
                            'name' => $attributeName,
                            'visible' => true,
                            'variation' => true,
                            'options' => [$attributeValue],
                        ];
                    });
                })
                ->flatten(1) // Flatten the array by one level
                ->groupBy('name')
                ->map(function ($group) {
                    return [
                        'name' => $group->first()['name'],
                        'visible' => true,
                        'variation' => true,
                        'options' => $group->pluck('options')->flatten()->unique()->toArray(),
                    ];
                })
                ->values()
                ->all();

            if(count($attributes)) {
                $productData['attributes'] = $attributes;
                $defaultAttributes = collect($attributes)->map(function ($attribute){
                    return [
                        'name' => $attribute['name'],
                        'option' => $attribute['options'][0]
                    ];
                });

                $productData['default_attributes'] = $defaultAttributes->toArray();
            }

            $productData['type'] = 'variable';
            $productData['sku'] = $product->barcode;

            if($mode == 'saved') {
                $wcProduct = $this->woocommerce->store('products', $productData);
            } else {
                $wcProduct =$this->woocommerce->update("products/{$product->wcProductId}", $productData);
            }
            $wcProductId = $wcProduct->id ?? $wcProduct->data->resource_id;
        } else {
            $wcProductId = 0;
            if($mode == 'saved' && is_null($product->wcProductId) && count($stocks)) {
                $wcProduct = $this->woocommerce->store('products', $productData);
                $wcProductId = $wcProduct->id ?? $wcProduct->data->resource_id;
            } else if($product->wcProductId) {
                $wcProduct =$this->woocommerce->update("products/{$product->wcProductId}", $productData);
                $wcProductId = $wcProduct->id ?? $wcProduct->data->resource_id;
            }
        }

        if($mode == 'saved' && is_null($product->wcProductId)) {
            $product->update(['wcProductId' => $wcProductId]);
        }

        $stocks->reject(function ($value) use ($wcProductId, $productData) {
                return $wcProductId === null;
            })
            ->each(function ($stock) use ($wcProductId, $productData) {
                if($stock->wcStockId) {
                    event(new StockSavingEvent('updated', $stock, $wcProductId, $productData));
                } else {
                    event(new StockSavingEvent('saved', $stock, $wcProductId, $productData));
                }
            return $stock;
        });
    }
}
