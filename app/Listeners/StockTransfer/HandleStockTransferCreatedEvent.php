<?php

namespace App\Listeners\StockTransfer;

use App\Events\StockTransfer\StockTransferCreatedEvent;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStockTransferCreatedEvent implements ShouldQueue
{
    /**
     * @var StockLogRepository
     */
    protected $stockLogRepository;

    /**
     * @var StockRepository
     */
    protected $stockRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(StockRepository $stockRepository, StockLogRepository $stockLogRepository)
    {
        $this->stockRepository = $stockRepository;
        $this->stockLogRepository = $stockLogRepository;
    }

    /**
     * Handle the event.
     *
     * @param StockTransferCreatedEvent $event
     * @return void
     */
    public function handle(StockTransferCreatedEvent $event)
    {
        //
    }
}
