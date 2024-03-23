<?php


namespace App\Repositories\Contracts;


use App\Models\StockTransfer;
use Illuminate\Validation\ValidationException;

interface StockTransferRepository extends BaseRepository
{
    /**
     * Update stock transfer
     *
     * @param StockTransfer $stockTransfer
     * @param array $data
     * @return \ArrayAccess
     * @throws ValidationException
     */
    public function updateStockTransfer(StockTransfer $stockTransfer, array $data): \ArrayAccess;

}
