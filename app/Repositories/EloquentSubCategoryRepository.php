<?php


namespace App\Repositories;

use App\Events\Woocommerce\SubCategorySavingEvent;
use App\Models\Branch;
use App\Repositories\Contracts\SubCategoryRepository;

class EloquentSubCategoryRepository extends EloquentBaseRepository implements SubCategoryRepository
{
    /**
     * @inheritDoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('code', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('category', function($query) use ($searchCriteria){
                    $query->where('name', 'like', $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        return parent::findBy($searchCriteria, $withTrashed);
    }

    /**
     * create or get subCategory by name
     * @param $subCategoryName
     * @return \ArrayAccess|null
     */
    public function createOrGetSubCategoryByName($subCategoryName, $categoryId)
    {
        $subCategory = $this->findOneBy(['name' => $subCategoryName]);
        if (!$subCategory) {
            $subCategory = $this->save([
                'name' => $subCategoryName,
                'categoryId' => $categoryId
            ]);
        }

        return $subCategory;
    }

    public function save(array $data): \ArrayAccess
    {
        $subCategory = parent::save($data);

        $ecomBranch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($ecomBranch) {
            event(new SubCategorySavingEvent('saved', $subCategory));
        }

        return $subCategory;
    }

    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        $subCategory = parent::update($model, $data);

        $ecomBranch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();
        if ($ecomBranch) {
            event(new SubCategorySavingEvent('updated', $subCategory));
        }

        return $subCategory;
    }
}
