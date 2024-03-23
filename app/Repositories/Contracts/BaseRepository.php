<?php

namespace App\Repositories\Contracts;

interface BaseRepository
{
    /**
     * find a resource by id
     *
     * @param mixed $id
     * @param bool $withTrashed
     * @return \ArrayAccess|null
     */
    public function findOne($id, $withTrashed = false): ?\ArrayAccess;

    /**
     * find a resource by criteria
     *
     * @param array $criteria
     * @param bool $withTrashed
     * @param bool $withLatest
     * @param string $latestBy
     * @return \ArrayAccess | null
     */
    public function findOneBy(array $criteria, $withTrashed = false,$withLatest = false, $latestBy = 'updated_at'): ?\ArrayAccess;

    /**
     * Search All resources
     *
     * @param array $searchCriteria
     * @param bool $withTrashed
     * @return mixed
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false);

    /**
     * save a resource
     *
     * @param array $data
     * @return \ArrayAccess
     */
    public function save(array $data): \ArrayAccess;

    /**
     * update a resource
     *
     * @param \ArrayAccess $model
     * @param array $data
     * @return \ArrayAccess
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess;

    /**
     * delete a resource
     *
     * @param \ArrayAccess $model
     * @return bool
     */
    public function delete(\ArrayAccess $model): bool;

    /**
     * patch a resource
     *
     * @param array $searchCriteria
     * @param array $data
     * @return \ArrayAccess
     */
    public function patch(array $searchCriteria, array $data): \ArrayAccess;

    /**
     * Search All resources
     *
     * @param array $searchCriteria
     * @param bool $withTrashed
     * @param bool $timestamp
     * @return mixed
     */
    public function findByWithDateRanges(array $searchCriteria = [], bool $withTrashed = false, $timestamp = true);
}
