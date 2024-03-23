<?php

namespace App\Events\Adjustment;

use App\Models\Adjustment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdjustmentCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Adjustment
     */
    public $adjustment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Adjustment $adjustment)
    {
        $this->adjustment = $adjustment;
    }
}
