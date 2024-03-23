<?php

namespace App\Providers;

use App\Events\Adjustment\AdjustmentCreatedEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProductReturn\OrderProductReturnCreatedEvent;
use App\Events\PasswordReset\PasswordResetEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\Product\ProductOpeningStockCreatedEvent;
use App\Events\Product\ProductUpdatedEvent;
use App\Events\Product\ProductVariantsCreatedEvent;
use App\Events\PurchaseProduct\PurchaseProductCreatedEvent;
use App\Events\PurchaseProductReturn\PurchaseProductReturnCreatedEvent;
use App\Events\Stock\StockCreatedEvent;
use App\Events\Stock\StockMovedEvent;
use App\Events\Stock\StockUpdatedEvent;
use App\Events\StockTransfer\StockTransferCreatedEvent;
use App\Events\StockTransfer\StockTransferUpdatedEvent;
use App\Events\Woocommerce\BrandSavingEvent;
use App\Events\Woocommerce\CategorySavingEvent;
use App\Events\Woocommerce\OrderUpdatingEvent;
use App\Events\Woocommerce\ProductSavingEvent;
use App\Events\Woocommerce\StockSavingEvent;
use App\Events\Woocommerce\SubCategorySavingEvent;
use App\Events\Woocommerce\TaxSavingEvent;
use App\Listeners\Adjustment\HandleAdjustmentCreatedEvent;
use App\Listeners\EmailNotificationSentListener;
use App\Listeners\Order\HandleOrderCreatedEvent;
use App\Listeners\OrderProduct\HandleOrderProductCreatedEvent;
use App\Listeners\OrderProductReturn\HandleOrderProductReturnCreatedEvent;
use App\Listeners\PasswordReset\HandlePasswordResetEvent;
use App\Listeners\Product\HandleProductCreatedEvent;
use App\Listeners\Product\HandleProductOpeningStockCreatedEvent;
use App\Listeners\Product\HandleProductUpdatedEvent;
use App\Listeners\Product\HandleProductVariantsCreatedEvent;
use App\Listeners\PurchaseProduct\HandlePurchaseProductCreatedEvent;
use App\Listeners\PurchaseProductReturn\HandlePurchaseProductReturnCreatedEvent;
use App\Listeners\Stock\HandleStockCreatedEvent;
use App\Listeners\Stock\HandleStockMovedEventListner;
use App\Listeners\Stock\HandleStockUpdatedEvent;
use App\Listeners\StockTransfer\HandleStockTransferCreatedEvent;
use App\Listeners\StockTransfer\HandleStockTransferUpdatedEvent;
use App\Listeners\Woocommerce\HandleBrandSavingEvent;
use App\Listeners\Woocommerce\HandleCategorySavingEvent;
use App\Listeners\Woocommerce\HandleOrderUpdatingEvent;
use App\Listeners\Woocommerce\HandleProductSavingEvent;
use App\Listeners\Woocommerce\HandleStockSavingEvent;
use App\Listeners\Woocommerce\HandleSubCategorySavingEvent;
use App\Listeners\Woocommerce\HandleTaxSavingEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NotificationSent::class => [
            EmailNotificationSentListener::class,
        ],

        PasswordResetEvent::class => [
            HandlePasswordResetEvent::class
        ],

        ProductCreatedEvent::class => [
            HandleProductCreatedEvent::class
        ],

        ProductUpdatedEvent::class => [
            HandleProductUpdatedEvent::class
        ],

        PurchaseProductCreatedEvent::class => [
            HandlePurchaseProductCreatedEvent::class
        ],

        OrderCreatedEvent::class => [
            HandleOrderCreatedEvent::class
        ],

        OrderProductCreatedEvent::class => [
            HandleOrderProductCreatedEvent::class
        ],

        StockTransferCreatedEvent::class => [
            HandleStockTransferCreatedEvent::class
        ],

        AdjustmentCreatedEvent::class => [
            HandleAdjustmentCreatedEvent::class
        ],

        PurchaseProductReturnCreatedEvent::class => [
            HandlePurchaseProductReturnCreatedEvent::class
        ],

        OrderProductReturnCreatedEvent::class => [
            HandleOrderProductReturnCreatedEvent::class
        ],

        ProductOpeningStockCreatedEvent::class => [
            HandleProductOpeningStockCreatedEvent::class
        ],

        StockCreatedEvent::class => [
            HandleStockCreatedEvent::class
        ],

        StockUpdatedEvent::class => [
            HandleStockUpdatedEvent::class
        ],

        StockTransferUpdatedEvent::class => [
            HandleStockTransferUpdatedEvent::class
        ],

        ProductVariantsCreatedEvent::class => [
            HandleProductVariantsCreatedEvent::class
        ],

        //related to woocommerce
        CategorySavingEvent::class => [
            HandleCategorySavingEvent::class
        ],
        SubCategorySavingEvent::class => [
            HandleSubCategorySavingEvent::class
        ],
        BrandSavingEvent::class => [
            HandleBrandSavingEvent::class
        ],
        TaxSavingEvent::class => [
            HandleTaxSavingEvent::class
        ],
        ProductSavingEvent::class => [
            HandleProductSavingEvent::class
        ],
        StockSavingEvent::class => [
            HandleStockSavingEvent::class
        ],
        OrderUpdatingEvent::class => [
            HandleOrderUpdatingEvent::class
        ],
        StockMovedEvent::class => [
            HandleStockMovedEventListner::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
