<?php


namespace App\Repositories\Contracts;


interface ProductRepository extends BaseRepository
{
    /**
     * @param array $searchCriteria
     * @return mixed
     */
    public function getProductGroupByStock(array $searchCriteria = []);

    /**
     * @param $request
     * @return mixed
     */
    public function stocks($request);

    /**
     * @param array $searchCriteria
     * @param bool $onlyTrashed
     * @return mixed
     */
    public function applyFilterInProductSearch(array $searchCriteria = [], bool $onlyTrashed = false);
}
