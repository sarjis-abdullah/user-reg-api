<?php

namespace App\Repositories\Contracts;

interface OrderProductReturnRepository extends BaseRepository
{
    /**
     * @param array $data
     * @return \ArrayAccess
     */
    public function saveReturnAbleProduct(array $data): \ArrayAccess;
}
