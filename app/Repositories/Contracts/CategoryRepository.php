<?php


namespace App\Repositories\Contracts;


interface CategoryRepository extends BaseRepository
{
    /**
     * create or get category by name
     *
     * @param string $categoryName
     * @return \ArrayAccess|null
     */
    public function createOrGetCategoryByName($categoryName);

}
