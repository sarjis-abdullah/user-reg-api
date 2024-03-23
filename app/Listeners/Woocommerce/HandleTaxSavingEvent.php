<?php

namespace App\Listeners\Woocommerce;

use App\Events\Woocommerce\TaxSavingEvent;
use App\Models\Tax;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTaxSavingEvent implements ShouldQueue
{
    use Queueable;

    protected $woocommerce;

    public function __construct(WoocomCommunicationService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    public function handle(TaxSavingEvent $event)
    {
        $mode = $event->mode;

        if ($mode == 'batch-create') {
            $this->batchCreateTaxes();
        } elseif ($mode == 'saved') {
            $this->saveOrUpdateTax($event->tax);
        } elseif ($mode == 'updated') {
            $this->saveOrUpdateTax($event->tax, true);
        }
    }

    protected function batchCreateTaxes()
    {
        $taxes = Tax::all();

        $mappedData = collect($taxes)->map(function ($tax) {
            return [
                'name' => $tax->title,
                'class' => 'standard',
                'rate' => $tax->amount
            ];
        });

        $taxesData = $this->woocommerce->store('taxes/batch', ['create' => $mappedData]);

        collect($taxesData->create)->each(function ($wcTax, $index) use ($mappedData) {
            if (isset($wcTax->id) && $wcTax->id !== 0) {
                $wcTaxId = $wcTax->id;
            } elseif (isset($wcTax->error)) {
                $wcTaxId = $wcTax->error->data->resource_id;
            } elseif (isset($wcTax->code) && $wcTax->code == 'term_exists') {
                $wcTaxId = $wcTax->data->resource_id;
            } else {
                $wcTaxId = null;
            }

            $item = [
                'name' => $mappedData[$index]['name'],
                'wcTaxId' => $wcTaxId
            ];

            return Tax::where('title', $item['name'])->update(['wcTaxId' => $item['wcTaxId']]);
        });
    }

    protected function saveOrUpdateTax(Tax $tax, $update = false)
    {
        $data = ['name' => $tax->title, 'class' => 'standard', 'rate' => (string) $tax->amount];

        if($update && $tax->wcTaxId) {
            $this->woocommerce->update("taxes/{$tax->wcTaxId}", $data);
        } else if(!$update && is_null($tax->wcTaxId)) {
            $wcTax = $this->woocommerce->store('taxes', $data);
            if($wcTax) {
                if (isset($wcTax->id) && $wcTax->id !== 0) {
                    $wcTaxId = $wcTax->id;
                } elseif (isset($wcTax->error)) {
                    $wcTaxId = $wcTax->error->data->resource_id;
                } elseif (isset($wcTax->code) && $wcTax->code == 'term_exists') {
                    $wcTaxId = $wcTax->data->resource_id;
                } else {
                    $wcTaxId = null;
                }

                $tax->update(['wcTaxId' => $wcTaxId]);
            }
        }
    }
}
