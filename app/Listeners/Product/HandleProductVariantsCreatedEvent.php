<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductVariantsCreatedEvent;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleProductVariantsCreatedEvent implements ShouldQueue
{
    /**
     * @var StockRepository
     */
    protected $stockRepository;

    /**
     * Create the event listener.
     *
     * @param StockRepository $stockRepository
     */
    public function __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    /**
     * Handle the event.
     *
     * @param ProductVariantsCreatedEvent $event
     * @return void
     */
    public function handle(ProductVariantsCreatedEvent $event)
    {
        $product = $event->product;
        $variations = $event->variations;

        //create or updated variation
        $results = collect($variations)->map(function ($variation) use ($product) {
            $stockData = [
                'createdByUserId' => $product->createdByUserId,
                'size' => $variation['size'] ?? null,
                'color' => $variation['color'] ?? null,
                'material' => $variation['material'] ?? null,
                'deleted_at' => null
            ];

            return $product->productVariations()->withTrashed()->updateOrCreate(
                [
                    'productId' => $product->id,
                    'size' => $variation['size'] ?? null,
                    'color' => $variation['color'] ?? null,
                    'material' => $variation['material'] ?? null
                ],
                $stockData
            );
        });

        // remove unused variation
        $product->productVariations()->whereNotIn('id', $results->pluck('id')->toArray())->delete();
    }
}
