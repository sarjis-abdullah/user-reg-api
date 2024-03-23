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
       
    }
}
