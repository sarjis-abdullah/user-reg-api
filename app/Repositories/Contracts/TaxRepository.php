<?php

namespace App\Repositories\Contracts;

interface TaxRepository extends BaseRepository
{
    /**
     * @param float $amount
     * @param string $type
     * @return mixed
     */
    public function createOrGetTaxByAmountAndType(float $amount, string $type);
}
