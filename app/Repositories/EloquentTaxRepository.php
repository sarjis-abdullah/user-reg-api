<?php

namespace App\Repositories;

use App\Events\Woocommerce\TaxSavingEvent;
use App\Models\Branch;
use App\Models\Tax;
use App\Repositories\Contracts\TaxRepository;

class EloquentTaxRepository extends EloquentBaseRepository implements TaxRepository
{
    public function save(array $data): \ArrayAccess
    {
        $tax = parent::save($data);

        $ecomBranch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($ecomBranch) {
            event(new TaxSavingEvent('saved', $tax));
        }

        return $tax;
    }

    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        $tax = parent::update($model, $data);

        $ecomBranch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($ecomBranch) {
            event(new TaxSavingEvent('updated', $tax));
        }

        return $tax;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('title', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('type', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('amount', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('action', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        return $this->findByWithDateRanges($searchCriteria, $withTrashed, true);
    }

    /**
     * @param float $amount
     * @param string $type
     * @return \ArrayAccess|null
     */
    public function createOrGetTaxByAmountAndType(float $amount, string $type): ?\ArrayAccess
    {
        $tax = $this->findOneBy(['amount' => $amount, 'type' => $type]);
        if (!$tax) {
            $tax = $this->save([
                'title' => ($amount.' '.(/*$type == Tax::TYPE_FLAT ? 'Tk' : */Tax::TYPE_PERCENTAGE)),
                'amount' => $amount,
                'type' => $type,
                'action' => Tax::ACTION_EXCLUSIVE
            ]);
        }

        return $tax;
    }
}
