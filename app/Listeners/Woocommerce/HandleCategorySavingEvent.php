<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\CategorySavingEvent;
use App\Models\Category;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleCategorySavingEvent implements ShouldQueue
{
    use Queueable;

    protected $woocommerce;

    public function __construct(WoocomCommunicationService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    public function handle(CategorySavingEvent $event)
    {
        $mode = $event->mode;

        if ($mode == 'batch-create') {
            $this->batchCreateCategories();
        } elseif ($mode == 'saved') {
            $this->saveOrUpdateCategory($event->category);
        } elseif ($mode == 'updated') {
            $this->saveOrUpdateCategory($event->category, true);
        }
    }

    protected function batchCreateCategories()
    {
        $categories = Category::all();

        // Divide categories into batches of 100
        $categoryBatches = $categories->chunk(100);

        foreach ($categoryBatches as $batch) {
            $mappedData = $batch->map(function ($category) {
                return ['name' => $category->name];
            })->values()->all();

            $categoriesData = $this->woocommerce->store('products/categories/batch', ['create' => $mappedData]);

            collect($categoriesData->create)->each(function ($wcCategory, $index) use ($mappedData) {
                if($wcCategory) {
                    if (isset($wcCategory->id) && $wcCategory->id !== 0) {
                        $wcCategoryId = $wcCategory->id;
                    } elseif (isset($wcCategory->error)) {
                        $wcCategoryId = $wcCategory->error->data->resource_id;
                    } elseif (isset($wcCategory->code) && $wcCategory->code == 'term_exists') {
                        $wcCategoryId = $wcCategory->data->resource_id;
                    } else {
                        $wcCategoryId = null;
                    }

                    $item = [
                        'name' => $mappedData[$index]['name'],
                        'wcCategoryId' => $wcCategoryId
                    ];

                    Category::where('name', $item['name'])->update(['wcCategoryId' => $item['wcCategoryId']]);
                }
            });
        }
    }

    protected function saveOrUpdateCategory(Category $category, $update = false)
    {
        $data = ['name' => $category->name];

        if($update && $category->wcCategoryId) {
            $this->woocommerce->update("products/categories/{$category->wcCategoryId}", $data);
        } else if(!$update && is_null($category->wcCategoryId)) {
            $wcCategory = $this->woocommerce->store('products/categories', $data);
            if($wcCategory) {
                if (isset($wcCategory->id) && $wcCategory->id !== 0) {
                    $wcCategoryId = $wcCategory->id;
                } elseif (isset($wcCategory->error)) {
                    $wcCategoryId = $wcCategory->error->data->resource_id;
                } elseif (isset($wcCategory->code) && $wcCategory->code == 'term_exists') {
                    $wcCategoryId = $wcCategory->data->resource_id;
                } else {
                    $wcCategoryId = null;
                }

                $category->update(['wcCategoryId' => $wcCategoryId]);
            }
        }
    }
}
