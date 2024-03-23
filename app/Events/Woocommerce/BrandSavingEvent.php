<?php

namespace App\Events\Woocommerce;

use App\Models\Brand;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BrandSavingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $mode;
    /**
     * @var Brand
     */
    public $brand;

    /**
     * Create a new event instance.
     *
     * @param string $mode
     * @param Brand|null $brand
     */
    public function __construct(string $mode, Brand $brand = null)
    {
        $this->mode = $mode;
        $this->brand = $brand;
    }
}
