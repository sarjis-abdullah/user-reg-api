<?php

namespace App\Events\Woocommerce;

use App\Models\SubCategory;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubCategorySavingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $mode;
    /**
     * @var SubCategory|null
     */
    public $subCategory;

    /**
     * Create a new event instance.
     *
     * @param string $mode
     * @param SubCategory|null $subCategory
     */
    public function __construct(string $mode, SubCategory $subCategory = null)
    {
        $this->mode = $mode;
        $this->subCategory = $subCategory;
    }
}
