<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EloquentBaseRepository implements BaseRepository
{
    use EloquentEagerLoadTrait;

    /**
     * @var Model
     */
    protected $model;

     /**
     * @var Model
     */
    protected $oldModel;

    /**
     * EloquentBaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * get the model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function findOne($id, $withTrashed = false): ?\ArrayAccess
    {
        $queryBuilder = $this->model;

        if (is_numeric($id)) {
            $item = $queryBuilder->find($id);
        }

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria, $withTrashed = false, $withLatest = false, $latestBy = 'created_at'): ?\ArrayAccess
    {
        $queryBuilder = $this->model->where($criteria);

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }

        if($withLatest) {
            $queryBuilder->latest($latestBy);
        }

        $item = $queryBuilder->first();

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 50; // it's needed for pagination
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';

        $this->validateOrderByField($orderBy);

        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }

        $queryBuilder = $this->applyEagerLoad($queryBuilder, $searchCriteria);

        if (isset($searchCriteria['rawOrder'])) {
            $queryBuilder->orderByRaw(DB::raw("FIELD(id, {$searchCriteria['id']})"));
        } else {
            $queryBuilder->orderBy($orderBy, $orderDirection);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }
    }

    /**
     * validate order by field
     *
     * @param string $orderBy
     */
    protected function validateOrderByField($orderBy)
    {
        $allowableFields = array_merge($this->model->getFillable(), ['id', 'created_at', 'updated_at']);
        if (!in_array($orderBy, $allowableFields)) {
            throw ValidationException::withMessages([
                'order_by' => ["You can't order with the field '" . $orderBy . "'"]
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function save(array $data): \ArrayAccess
    {
        // set createdBy by user from loggedInUser
        if (!isset($data['createdByUserId']) || $data['createdByUserId'] === null) {
            $loggedInUser = $this->getLoggedInUser();
            if ($loggedInUser instanceof User) {
                $data['createdByUserId'] = $loggedInUser->id;
            }
        }

        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        $this->oldModel = clone $model;

        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {

            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }


        // set updatedBy by user from loggedInUser
        if (in_array('updatedByUserId', $fillAbleProperties) && !isset($data['updatedByUserId'])) {
            $loggedInUser = $this->getLoggedInUser();
            if ($loggedInUser instanceof User) {
                $model['updatedByUserId'] = $loggedInUser->id;
            }
        }

        // update the model
        $model->save();
        // get updated model from database
        $model = $this->findOne($model->id);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function delete(\ArrayAccess $model): bool
    {
        return $model->delete();
    }

    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @param string $operator
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder(
        $queryBuilder,
        array $searchCriteria = [],
        string $operator = '='
    ): object
    {
        unset($searchCriteria['include'], $searchCriteria['eagerLoad'], $searchCriteria['rawOrder'], $searchCriteria['detailed'], $searchCriteria['withoutPagination']); //don't need that field for query. only needed for transformer.

        foreach ($searchCriteria as $key => $value) {

            //skip pagination related query params
            if (in_array($key, ['page', 'per_page', 'order_by', 'order_direction'])) {
                continue;
            }

            if ($value == 'null') {
                $queryBuilder->whereNull($key);
            } else {
                if ($value == 'notNull') {
                    $queryBuilder->whereNotNull($key);
                } else {
                    //we can pass multiple params for a filter with commas
                    if (is_array($value)) {
                        $allValues = $value;
                    } else {
                        $allValues = explode(',', $value);
                    }

                    if (count($allValues) > 1) {
                        $queryBuilder->whereIn($key, $allValues);
                    } else {
                        if ($operator == 'like') {
                            $queryBuilder->where($key, $operator, '%' . $value . '%');
                        } else {
                            $queryBuilder->where($key, $operator, $value);
                        }
                    }
                }
            }
        }

        return $queryBuilder;
    }


    /**
     * @inheritdoc
     */
    public function patch(array $searchCriteria, array $data): \ArrayAccess
    {
        $model = $this->findOneBy($searchCriteria, true);

        if ($model instanceof Model) {
            if ($model->trashed()) {
                $model->restore();
            }

            $model = $this->update($model, $data);
            return $model;
        } else {
            return $this->save($data);
        }

    }

    /**
     * get modified fields
     *
     * @param $model
     * @param $data
     * @return array
     */
    public function getModifiedFields($model, $data)
    {
        $fillAbleProperties = $model->getFillable();

        foreach ($data as $key => $value) {
            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        return $model->getDirty();
    }

    /**
     * get loggedIn user
     *
     * @return User
     */
    protected function getLoggedInUser(): User
    {
        if (\Auth::user() instanceof User) {
            return \Auth::user();
        } else {
            return new User();
        }
    }

    /**
     * paginate custom data
     *
     * @param array $items
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected function paginateData($items, $perPage = 15, $page = null, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    /**
     * generate event options for model
     *
     * @param array $additionalData
     * @param bool $addRequest
     * @return array
     */
    public function generateEventOptionsForModel($additionalData = [], $addRequest = true)
    {
        $options['request'] = [];
        if ($addRequest) {
            $request = request();
            $options['request'] = $request->toArray();
        }

        $options['request']['loggedInUserId'] = $options['request']['loggedInUserId'] ?? $this->getLoggedInUser()->id;
        if ($this->oldModel instanceof \ArrayAccess) {
            $options['oldModel'] = $this->oldModel;
        }

        return array_merge($options, $additionalData);
    }

    //TODO may require to filter date ranges with date/datetime/startDate/endDate etc. column instead of timestamp(created_at)
    //TODO for now timestamp = true means date filter is now workable for only created_at field

    public function findByWithDateRanges(array $searchCriteria = [], $withTrashed = false, $timestamp = true)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
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
            return $queryBuilder->paginate($limit);
        } else {
            return $queryBuilder->get();
        }
    }
}
