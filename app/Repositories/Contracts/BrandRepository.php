<?php


namespace App\Repositories\Contracts;


interface BrandRepository extends BaseRepository
{
    /**
     * create or get brand by name
     *
     * @param string $brandName
     * @param string $companyId
     * @return \ArrayAccess|null
     */
    public function createOrGetBrandByName(string $brandName, int $companyId);

}
