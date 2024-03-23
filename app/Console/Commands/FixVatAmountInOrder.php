<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderRepository;
use Illuminate\Console\Command;

class FixVatAmountInOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:fix-tax';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Order Tax Amount';
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orderProductRepository = app(OrderProductRepository::class);
        $this->orderRepository = app(OrderRepository::class);

        $this->orderRepository->getModel()
            ->whereHas('orderProducts', function($q) {
                $q->where(function ($query) {
                    $query->where('tax', '>', 0)
                        ->orWhereNotNull('taxId');
                });
                $q->where(function ($query) {
                    $query->where('discountedUnitPrice', '>', 0)
                        ->orWhere('unitPrice', '>', 0);
                });
            })
            ->chunk(100, function ($orders) use($orderProductRepository) {
                // Process each chunk of orders here
                foreach ($orders as $order) {
                    $totalTax = collect($order->orderProducts)->map(function ($item) use ($orderProductRepository) {
                        $unitPrice = $item->discountedUnitPrice ?? $item->unitPrice;
                        $tax = $item->taxId ? ($unitPrice * $item->quantity * $item->getTax->amount) / 100 : 0;
                        return $orderProductRepository->update($item, ['tax' => $tax]);
                    })->sum('tax');

                    $this->orderRepository->update($order, ['tax' => $totalTax]);
                }
            });
    }
}
