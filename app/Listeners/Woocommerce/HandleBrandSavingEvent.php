<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\BrandSavingEvent;
use App\Models\Brand;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleBrandSavingEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @var WoocomCommunicationService
     */
    protected $woocommerce;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(WoocomCommunicationService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    /**
     * @param BrandSavingEvent $event
     * @return void
     */
    public function handle(BrandSavingEvent $event)
    {
        $mode = $event->mode;

        if ($mode == 'batch-create') {
            $this->batchCreateTaxes();
        } elseif ($mode == 'saved') {
            $this->saveOrUpdateBrand($event->brand);
        } elseif ($mode == 'updated') {
            $this->saveOrUpdateBrand($event->brand, true);
        }
    }

    protected function batchCreateTaxes()
    {
        $brands = Brand::all();

        collect($brands)->each(function ($brand) {
            self::saveOrUpdateBrand($brand);
        });
    }

    protected function saveOrUpdateBrand(Brand $brand, $update = false)
    {
        sleep(1);
        $data = ['name' => $brand->name];

        if($update && $brand->wcBrandId) {
            $this->woocommerce->update("products/brands/{$brand->wcBrandId}", $data);
        } else if(!$update && is_null($brand->wcBrandId)) {
            $wcBrand = $this->woocommerce->store('products/brands', $data);

            if($wcBrand) {
                if (isset($wcBrand->id) && $wcBrand->id !== 0) {
                    $wcBrandId = $wcBrand->id;
                } elseif (isset($wcBrand->error)) {
                    $wcBrandId = $wcBrand->error->data->resource_id;
                } elseif (isset($wcBrand->code) && $wcBrand->code == 'term_exists') {
                    $wcBrandId = $wcBrand->data->resource_id;
                } else {
                    $wcBrandId = null;
                }

                $brand->update(['wcBrandId' => $wcBrandId]);
            }
        }
    }
}
