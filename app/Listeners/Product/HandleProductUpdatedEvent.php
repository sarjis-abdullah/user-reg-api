<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductUpdatedEvent;
use App\Listeners\CommonListenerFeatures;
use App\Models\Attachment;
use App\Models\Product;
use App\Services\Helpers\BarcodeGenerateHelper;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleProductUpdatedEvent implements ShouldQueue
{
    use CommonListenerFeatures;

    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        $product = $event->product;
        $eventOptions = $event->options;
        $oldProduct = $eventOptions['oldModel'];

        $hasBarcodeChanged = $this->hasAFieldValueChanged($product, $oldProduct, 'barcode');

        if($hasBarcodeChanged) {
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
}
