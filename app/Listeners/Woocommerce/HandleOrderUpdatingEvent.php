<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\OrderUpdatingEvent;
use App\Listeners\CommonListenerFeatures;
use App\Models\Order;
use App\Repositories\Contracts\OrderLogRepository;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleOrderUpdatingEvent implements ShouldQueue
{
    use CommonListenerFeatures;
    use Queueable;

    /**
     * @var WoocomCommunicationService
     */
    protected WoocomCommunicationService $woocommerce;
    /**
     * @var OrderLogRepository
     */
    protected OrderLogRepository $orderLogRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(WoocomCommunicationService $woocommerce, OrderLogRepository $orderLogRepository)
    {
        $this->orderLogRepository = $orderLogRepository;
        $this->woocommerce = $woocommerce;
    }

    /**
     * Handle the event.
     *
     * @param OrderUpdatingEvent $event
     * @return void
     */
    public function handle(OrderUpdatingEvent $event)
    {
        $order = $event->order;
        $eventOptions = $event->options;
        $oldOrderItem = $eventOptions['oldModel'];

        $hasStatusChanged = $this->hasAFieldValueChanged($order, $oldOrderItem, 'status');
        $hasPaymentStatusChanged = $this->hasAFieldValueChanged($order, $oldOrderItem, 'paymentStatus');

        if(($hasStatusChanged || $hasPaymentStatusChanged) &&
            in_array($order->status, [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_CANCELLED,
                Order::STATUS_COMPLETED,
                Order::STATUS_ON_HOLD,
                Order::STATUS_REFUNDED,
                Order::STATUS_TRASH,
            ])
        ) {
            $this->createOrderLog($order, $oldOrderItem);

            $this->woocommerce->update("orders/$order->referenceId", ['status' => $order->status]);
        }
    }

    protected function createOrderLog($order, $oldOrder): void
    {
        $orderLogData = [
            'createdByUserId' => $order->updatedByUserId,
            'orderId' => $order->id,
            'status' => $order->status,
            'paymentStatus' => $order->paymentStatus,
            'deliveryStatus' => $order->status,
        ];

        //TODO: make more dynamic order comment
        $orderLogData['comment'] = match ($order->status) {
            Order::STATUS_PENDING => 'Your order is pending.',
            Order::STATUS_PROCESSING => 'Your order is in processing.',
            Order::STATUS_SHIPPED => 'Your order is in shipping.',
            Order::STATUS_CANCELLED => 'Your order is cancelled.',
            Order::STATUS_COMPLETED => 'Your order is in completed.',
            Order::STATUS_ON_HOLD => 'Your order is hold.',
            Order::STATUS_REFUNDED => 'Your order is refunded.',
            Order::STATUS_TRASH => 'Your order is trashed.',
        };

        $this->orderLogRepository->save($orderLogData);
    }
}
