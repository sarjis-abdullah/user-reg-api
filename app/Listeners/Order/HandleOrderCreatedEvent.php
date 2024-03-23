<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Attachment;
use App\Models\Coupon;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepository;
use App\Services\Helpers\BarcodeGenerateHelper;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleOrderCreatedEvent implements ShouldQueue
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Handle the event.
     *
     * @param OrderCreatedEvent $event
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
        $updatedOrderValue = $event->updatedOrderData;

        $order = $event->order;

        $profitAmount = $order->orderProducts->sum('profitAmount');

        $grossProfit = $profitAmount - $order->discount;

        $orderData = array_merge($updatedOrderValue,  ['profitAmount' => $profitAmount, 'grossProfit' => $grossProfit]);

        $this->orderRepository->update($order, $orderData);

        if (!empty($order->invoice)) {
            $barcodeData['resourceId'] = $order->id;
            $barcodeData['barcode'] = $order->invoice;
            $barcodeData['barcodeType'] = Product::BARCODE_TYPE_CODE_128;
            $barcodeData['attachmentType'] = Attachment::ATTACHMENT_TYPE_INVOICE;

            BarcodeGenerateHelper::generateBarcode($barcodeData);
        }

        if($order->couponId) {
            if($order->coupon instanceof Coupon && $order->coupon->to === Coupon::TO_INDIVIDUAL_CUSTOMER) {
                $order->coupon->couponCustomers()->where('customerId', $order->customerId)->increment('couponUsage');
            }
        }
    }
}
