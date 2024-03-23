<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderProductReturnRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Console\Command;

class PreviousStockSaleProfitCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'previous-stock-sale-profit:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $stockRepo = app(StockRepository::class);
        $stockLogRepo = app(StockLogRepository::class);
        $orderRepo = app(OrderRepository::class);
        $orderProductRepo = app(OrderProductRepository::class);
        $orderProductReturnRepo = app(OrderProductReturnRepository::class);

        $this->info('Getting all existing stocks to update unit profit');
        $allStocks = $stockRepo->getModel()->all();

        $this->info('Looping through '.  count($allStocks) .' stocks data!');

        foreach ($allStocks as $stock) {
            $unitProfit = $stock->unitPrice - $stock->unitCost;
            $stockRepo->update($stock, ['unitProfit' => $unitProfit, 'stockProfit' => 0, 'discountAmount' => 0, 'grossProfit' => 0]);

            $this->info('Get and update stock('. $stock->id. ') related order products data...');
            $orderProducts = $orderProductRepo->getModel()->where('stockId', $stock->id)->get();

            foreach ($orderProducts as $orderProduct) {
                $profitAmount = round($unitProfit * $orderProduct->quantity,2);
                $grossProfit = round($profitAmount - $orderProduct->discount,2);
                $orderProductRepo->update($orderProduct, ['profitAmount' => $profitAmount, 'grossProfit' => $grossProfit]);

                $stockLog = $orderProduct->stockLog;
                $stockLogRepo->update($stockLog, ['profitAmount' => $profitAmount, 'discountAmount' => $orderProduct->discount]);

                $stockProfit = round($stock->stockProfit + $profitAmount,2);
                $stockDiscountAmount = round($stock->discountAmount + $orderProduct->discount,2);
                $stockGrossProfit = round($stock->grossProfit + ($profitAmount - $orderProduct->discount),2);

                $stockRepo->update($stock, [
                    'stockProfit' => $stockProfit,
                    'discountAmount' => $stockDiscountAmount,
                    'grossProfit' => $stockGrossProfit
                ]);
            }
        }

        $this->info('All Stock, Stock logs & Stock order products update finished.');

        $this->info('Get and updated all orders which discount amount is below 0.');

        $orderIdsWhichHasNegativeDiscount = $orderRepo->getModel()->where('discount', '<', 0)->pluck('id')->toArray();

        $orderRepo->getModel()->whereIn('id', $orderIdsWhichHasNegativeDiscount)->update(['discount' => 0]);

        $this->info('Get and updated all orders and update profit.');

        $orders = $orderRepo->getModel()->get();

        foreach ($orders as $order) {
            $orderProfitAmount = $order->orderProducts->sum('profitAmount');
            $orderGrossProfit = $orderProfitAmount - $order->discount;

            $orderRepo->update($order, ['profitAmount' => $orderProfitAmount, 'grossProfit' => $orderGrossProfit]);

            $orderProductReturns = $orderProductReturnRepo->getModel()->where('orderId', $order->id)->get();

            foreach($orderProductReturns as $orderProductReturn) {
                $orderProduct = $orderProductReturn->orderProduct;
                $stock = $orderProduct->stock ?? $stockRepo->findOne($orderProduct->stockId);

                if($stock instanceof Stock) {
                    $profitAmount = round($stock->unitProfit * $orderProductReturn->quantity,2);
                    $discountAmount = round(($orderProduct->unitPrice - $orderProduct->discountedUnitPrice) * $orderProductReturn->quantity, 2);

                    $orderProductReturnRepo->update($orderProductReturn, ['profitAmount' => $profitAmount, 'discountAmount' => $discountAmount]);

                    $stockLog = $orderProductReturn->stockLog;
                    $stockLogRepo->update($stockLog, ['profitAmount' => $profitAmount, 'discountAmount' => $discountAmount]);

                    $stockProfit = $stock->stockProfit - $profitAmount;
                    $stockDiscountAmount = $stock->discountAmount - $discountAmount;
                    $stockGrossProfit = round($stock->grossProfit - ($profitAmount - $discountAmount), 2);

                    $stockRepo->update($stock, [
                        'stockProfit' => $stockProfit,
                        'discountAmount' => $stockDiscountAmount,
                        'grossProfit' => $stockGrossProfit
                    ]);
                }
            }
        }

        $this->info('All orders and orders returns and related stock log update finished.');

        return true;
    }
}
