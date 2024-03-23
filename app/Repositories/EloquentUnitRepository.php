<?php


namespace App\Repositories;


use App\Repositories\Contracts\UnitRepository;

class EloquentUnitRepository extends EloquentBaseRepository implements UnitRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        return $this->findByWithDateRanges($searchCriteria, $withTrashed, true);
    }
    /**
     * create or get unit by name
     *
     * @param string $unitName
     * @return \ArrayAccess|null
     */
    public function createOrGetUnitByName(string $unitName)
    {
        $unit = $this->findOneBy(['name' => $unitName]);
        if (!$unit) {
            $unit = $this->save(['name' => $unitName]);
        }

        return $unit;
    }
}
