<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\SubCategorySavingEvent;
use App\Models\SubCategory;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleSubCategorySavingEvent implements ShouldQueue
{
    use Queueable;

    protected $woocommerce;

    public function __construct(WoocomCommunicationService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    public function handle(SubCategorySavingEvent $event)
    {
        $mode = $event->mode;

        if ($mode == 'batch-create') {
            $this->batchCreateCategories();
        } elseif ($mode == 'saved') {
            $this->saveOrUpdateCategory($event->subCategory);
        } elseif ($mode == 'updated') {
            $this->saveOrUpdateCategory($event->subCategory, true);
        }
    }

    protected function batchCreateCategories()
    {
        $categories = SubCategory::all();

        // Divide categories into batches of 100
        $categoryBatches = $categories->chunk(100);

        foreach ($categoryBatches as $batch) {
            $mappedData = collect($batch)
                ->reject(function ($subCategory) {
                    return empty($subCategory->name) || ($subCategory->category && $subCategory->category->wcCategoryId) == null;
                })
                ->map(function ($subCategory) {
                    return [
                        'name' => $subCategory->name,
                        'parent' => $subCategory->category->wcCategoryId,
                    ];
                })
                ->values()
                ->all();

            $categoriesData = $this->woocommerce->store('products/categories/batch', ['create' => $mappedData]);

            collect($categoriesData->create)->each(function ($wcSubCategory, $index) use ($mappedData) {
                if($wcSubCategory) {
                    if (isset($wcSubCategory->id) && $wcSubCategory->id !== 0) {
                        $wcSubCategoryId = $wcSubCategory->id;
                    } elseif (isset($wcSubCategory->error)) {
                        $wcSubCategoryId = $wcSubCategory->error->data->resource_id;
                    } elseif (isset($wcSubCategory->code) && $wcSubCategory->code == 'term_exists') {
                        $wcSubCategoryId = $wcSubCategory->data->resource_id;
                    } else {
                        $wcSubCategoryId = null;
                    }

                    $item = [
                        'name' => $mappedData[$index]['name'],
                        'wcSubCategoryId' => $wcSubCategoryId
                    ];

                    SubCategory::where('name', $item['name'])->update(['wcSubCategoryId' => $item['wcSubCategoryId']]);
                }
            });
        }
    }

    protected function saveOrUpdateCategory(SubCategory $category, $update = false)
    {
        if(empty($category->name) || $category->category && $category->category->wcCategoryId == null) return false;

        $data = ['name' => $category->name, 'parent' => $category->category->wcCategoryId];

        if($update && $category->wcSubCategoryId) {
            $this->woocommerce->update("products/categories/{$category->wcSubCategoryId}", $data);
        } else if(!$update && is_null($category->wcSubCategoryId)) {
            $wcSubCategory = $this->woocommerce->store('products/categories', $data);
            if($wcSubCategory) {
                if (isset($wcSubCategory->id) && $wcSubCategory->id !== 0) {
                    $wcSubCategoryId = $wcSubCategory->id;
                } elseif (isset($wcSubCategory->error)) {
                    $wcSubCategoryId = $wcSubCategory->error->data->resource_id;
                } elseif (isset($wcSubCategory->code) && $wcSubCategory->code == 'term_exists') {
                    $wcSubCategoryId = $wcSubCategory->data->resource_id;
                } else {
                    $wcSubCategoryId = null;
                }

                $category->update(['wcSubCategoryId' => $wcSubCategoryId]);
            }
        }
    }
}
