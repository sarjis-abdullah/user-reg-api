<?php

namespace App\Repositories;

use App\Events\Adjustment\AdjustmentCreatedEvent;
use App\Repositories\Contracts\AdjustmentRepository;
use App\Repositories\Contracts\StockRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentAdjustmentRepository extends EloquentBaseRepository implements AdjustmentRepository
{
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['query'])) {
            $stockRepository = app(StockRepository::class);
            $stockIds = $stockRepository->model
                ->whereHas('product', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                    $query->orWhere('barcode', 'like', '%' . $searchCriteria['query'] . '%');
                    $query->when(isset($searchCriteria['withDeletedStock']), function ($q) {
                       $q->withTrashed();
                    });
                })
                ->when(isset($searchCriteria['withDeletedStock']), function ($q) {
                    $q->withTrashed();
                })
                ->pluck('id')->toArray();

            $searchCriteria['id'] = $this->model
                ->whereIn('stockId', $stockIds)
                ->pluck('id')
                ->toArray();

            unset($searchCriteria['query']);
        }

        if(isset($searchCriteria['withDeletedStock'])){
            unset($searchCriteria['withDeletedStock']);
        }

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('date', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('date', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }
        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        if (empty($searchCriteria['withoutPagination'])) {
            $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1;
            return $queryBuilder->paginate($limit, ['*'], 'page', $page);
        } else {
            return $queryBuilder->get();
        }
    }

    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $adjustments = collect($data['products'])->map(function ($item) use ($data) {
            $data = [
                "branchId" =>  $data['branchId'],
                "reason" =>  $data['reason'],
                "adjustmentBy" =>  $data['adjustmentBy'],
                "date" =>  $data['date'],
                "stockId" =>  $item['stockId'],
                "quantity" =>  $item['quantity'],
                "type" =>  $item['type'],
            ];

            return parent::save($data);
        });

        DB::commit();

        $adjustments->each(function ($adjustment) {
            event(new AdjustmentCreatedEvent($adjustment));
        });

        return $adjustments;
    }

}
