<?php


namespace App\Repositories\Contracts;


interface CompanyRepository extends BaseRepository
{
    /**
     * create or get company by name
     *
     * @param string $companyName
     * @return \ArrayAccess|null
     */
    public function createOrGetCompanyByName($companyName);
}
