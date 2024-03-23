<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Contracts\IncomeRepository;
use App\Repositories\Contracts\PaymentRepository;
use ArrayAccess;
use Carbon\Carbon;

class EloquentIncomeRepository extends EloquentBaseRepository implements IncomeRepository
{
   /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('date', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('date', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }

        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('sourceOfIncome', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('category', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }

        $withSummary = false;
        if (!empty($searchCriteria['withSummary'])) {
            unset($searchCriteria['withSummary']);
            $withSummary = true;
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $summary = [];

        if ($withSummary) {
            $allData = $queryBuilder->get();
            $summary['numberOfCategory'] = $allData->unique('categoryId')->count();
            $summary['totalIncome'] = $allData->sum('amount');
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $incomes = $queryBuilder->paginate($limit);
        } else {
            $incomes =  $queryBuilder->get();
        }


        $pageWiseSummary = [];

        $pageWiseSummary['numberOfCategory'] = $incomes->unique('categoryId')->count();
        $pageWiseSummary['totalIncome'] = $incomes->sum('amount');

        if ($withSummary){
            return ['incomes' => $incomes, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        }

        return ['incomes' => $incomes, 'pageWiseSummary' => $pageWiseSummary];
    }


    /**
     * @param array $data
     * @return ArrayAccess
     */
    public function save(array $data): ArrayAccess
    {
        $income = parent::save($data);

        if(isset($data['payment'])) {
            $paymentData = $data['payment'];
            $paymentData['status'] = Payment::STATUS_SUCCESS;
            $paymentData['cashFlow'] = Payment::CASH_FLOW_IN;
            $paymentData['paymentableId'] = $income->id;
            $paymentData['paymentableType'] = Payment::PAYMENT_SOURCE_INCOME;
            $paymentData['payType'] = Payment::PAY_TYPE_INCOME;
            $paymentData['receivedByUserId'] = $income->createdByUserId;
            $paymentData['date'] = Carbon::now();
            $paymentData['amount'] = $income->amount;
            $paymentData['method'] = Payment::METHOD_CASH;
            $paymentRepository = app(PaymentRepository::class);
            $paymentRepository->save($paymentData);
        }
        return $income;
    }
}
