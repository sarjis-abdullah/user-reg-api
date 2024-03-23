<?php


namespace App\Repositories;


use App\Events\Woocommerce\BrandSavingEvent;
use App\Models\Branch;
use App\Models\Brand;
use App\Repositories\Contracts\BrandRepository;

class EloquentBrandRepository extends EloquentBaseRepository implements BrandRepository
{

    public function save(array $data): \ArrayAccess
    {
        $brand = parent::save($data);

        $ecomBranch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($ecomBranch) {
            event(new BrandSavingEvent('saved', $brand));
        }

        return $brand;
    }

    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        $brand = parent::update($model, $data);

        $ecomBranch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($ecomBranch) {
            event(new BrandSavingEvent('updated', $brand));
        }

        return $brand;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('origin', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('company', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
        }

        return parent::findBy($searchCriteria);
    }

    /**
     * create or get brand by name
     *
     * @param string $brandName
     * @param int $companyId
     * @return \ArrayAccess|null
     */
    public function createOrGetBrandByName(string $brandName, int $companyId)
    {
        $brand = $this->findOneBy(['name' => $brandName]);
        if (!$brand) {
            $brand = $this->save(['name' => $brandName, 'companyId' => $companyId]);
        }

        return $brand;
    }
}
