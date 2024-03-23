<?php

namespace App\Http\Requests\StockTransfer;

use App\Http\Requests\Request;
use App\Models\StockTransfer;
use App\Rules\ValidateStockTransferStatus;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'list:numeric',
            'createdByUserId' => 'list:numeric',
            'shippedByUserId' => 'list:numeric',
            'updatedByUserId' => 'list:numeric',
            'branchId' => 'list:numeric',
            'fromBranchId' => 'list:numeric',
            'toBranchId' => 'list:numeric',
            'referenceNumber' => 'string',
            'deliveryMethod' => 'string',
            'query' => 'string',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d',
            'withoutPagination' => 'sometimes|integer',
            'status' => 'in:' . StockTransfer::STATUS_PENDING . ',' . StockTransfer::STATUS_CANCELLED . ',' . StockTransfer::STATUS_DECLINED . ',' . StockTransfer::STATUS_SHIPPED . ',' . StockTransfer::STATUS_RECEIVED,
            'statusList' =>  ['list:string', new ValidateStockTransferStatus]
        ];
    }
}
