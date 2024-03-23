<?php

namespace App\Events\Woocommerce;

use App\Models\Tax;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaxSavingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $mode;
    /**
     * @var Tax|null
     */
    public $tax;

    /**
     * Create a new event instance.
     *
     * @param string $mode
     * @param Tax|null $tax
     */
    public function __construct(string $mode, Tax $tax = null)
    {
        $this->mode = $mode;
        $this->tax = $tax;
    }
}
