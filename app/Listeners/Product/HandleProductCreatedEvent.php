<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductCreatedEvent;
use App\Models\Attachment;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepository;
use App\Services\Helpers\BarcodeGenerateHelper;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleProductCreatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param ProductCreatedEvent $event
     * @return void
     */
    public function handle(ProductCreatedEvent $event)
    {
        $product = $event->product;
        $barcodeType = empty($event->barcodeType) ?  Product::BARCODE_TYPE_CODE_128 : $event->barcodeType;

        if (!empty($product->barcode)) {
            $barcodeData['resourceId'] = $product->id;
            $barcodeData['barcode'] = $product->barcode;
            $barcodeData['barcodeType'] = $barcodeType;
            $barcodeData['attachmentType'] = Attachment::ATTACHMENT_TYPE_PRODUCT_BARCODE;

            BarcodeGenerateHelper::generateBarcode($barcodeData);
        }
    }
}
