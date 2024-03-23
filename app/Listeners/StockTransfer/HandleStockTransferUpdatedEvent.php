<?php

namespace App\Listeners\StockTransfer;

use App\Events\StockTransfer\StockTransferUpdatedEvent;
use App\Models\StockTransfer;
use App\Notifications\ProductTransfer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStockTransferUpdatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param StockTransferUpdatedEvent $event
     * @return void
     */
    public function handle(StockTransferUpdatedEvent $event)
    {
        $status = $event->status;
        $stockTransfer = $event->stockTransfer;
        $toBranch = $stockTransfer->toBranch;
        $fromBranch = $stockTransfer->fromBranch;

        if($status == StockTransfer::STATUS_SHIPPED) {
            $users = $toBranch->adminUserRoles->map(fn ($userRole) => $userRole->user && $userRole->user->isActive);

            $data = [
                'subject' => 'Products Shipped!',
                'link' => sprintf('/stock-transfer/stock-transfer-details/?id=%s&from=receivedstockTransfer', $stockTransfer->id),
                'appDescription' => sprintf('Products shipped to your branch from %s', $fromBranch->name),
                'mailDescription' => sprintf('Products shipped to your branch from %s ', $fromBranch->name),
                'fromBranch' => $fromBranch->name,
                'toBranch' => $toBranch->name,
            ];

            $users->each(function ($user) use ($stockTransfer, $data) {
                $user->notify(new ProductTransfer($stockTransfer, $data));
            });
        } else if($status == StockTransfer::STATUS_DECLINED) {
            $users = $fromBranch->adminUserRoles->map(fn ($userRole) => $userRole->user && $userRole->user->isActive);

            $data = [
                'subject' => 'Products Shipment Declined!',
                'link' => sprintf('/stock-transfer/stock-transfer-details/?id=%s&from=stockTransferList', $stockTransfer->id),
                'appDescription' => sprintf('Products shipment declined from branch %s ', $toBranch->name),
                'mailDescription' => sprintf('Products shipment declined from branch %s ', $toBranch->name),
                'fromBranch' => $fromBranch->name,
                'toBranch' => $toBranch->name,
            ];

            $users->each(function ($user) use ($stockTransfer, $data) {
                $user->notify(new ProductTransfer($stockTransfer, $data));
            });
        } else if ($status == StockTransfer::STATUS_RECEIVED) {
            $users = $fromBranch->adminUserRoles->map(fn ($userRole) => $userRole->user && $userRole->user->isActive);

            $data = [
                'subject' => 'Products Shipment Received!',
                'link' => sprintf('/stock-transfer/stock-transfer-details/?id=%s&from=stockTransferList', $stockTransfer->id),
                'appDescription' => sprintf('Products shipment received by %s branch', $toBranch->name),
                'mailDescription' => sprintf('Products shipment received by %s branch', $toBranch->name),
                'fromBranch' => $fromBranch->name,
                'toBranch' => $toBranch->name,
            ];

            $users->each(function ($user) use ($stockTransfer, $data) {
                $user->notify(new ProductTransfer($stockTransfer, $data));
            });
        }
    }
}
