<?php


namespace App\Repositories\Contracts;


interface UnitRepository extends BaseRepository
{
    /**
     * create or get unit by name
     *
     * @param string $unitName
     * @return \ArrayAccess|null
     */
    public function createOrGetUnitByName(string $unitName);
}
