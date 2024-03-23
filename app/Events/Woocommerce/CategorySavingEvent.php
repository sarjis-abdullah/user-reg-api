<?php

namespace App\Events\Woocommerce;

use App\Models\Category;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategorySavingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $mode;
    /**
     * @var Category|null
     */
    public $category;

    /**
     * Create a new event instance.
     *
     * @param string $mode
     * @param Category|null $category
     */
    public function __construct(string $mode, Category $category = null)
    {
        $this->mode = $mode;
        $this->category = $category;
    }
}
