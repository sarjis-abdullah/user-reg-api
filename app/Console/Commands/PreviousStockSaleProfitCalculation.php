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
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       
        
    }
}
