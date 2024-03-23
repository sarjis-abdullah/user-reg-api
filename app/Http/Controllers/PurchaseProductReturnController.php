<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseProductReturn\IndexRequest;
use App\Http\Requests\PurchaseProductReturn\StoreRequest;
use App\Http\Resources\PurchaseProductReturnResource;
use App\Models\PurchaseProductReturn;
use App\Repositories\Contracts\PurchaseProductReturnRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseProductReturnController extends Controller
{
    /**
     * @var PurchaseProductReturnRepository
     */
    protected $purchaseProductReturnRepository;

    /**
     * PurchaseProductReturnController constructor.
     * @param PurchaseProductReturnRepository $purchaseProductReturnRepository
     */
    public function __construct(PurchaseProductReturnRepository $purchaseProductReturnRepository)
    {
        $this->purchaseProductReturnRepository = $purchaseProductReturnRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $purchaseProductReturns = $this->purchaseProductReturnRepository->findBy($request->all());

        $purchaseProductReturnResources = PurchaseProductReturnResource::collection(!empty($purchaseProductReturns['summary']) ? $purchaseProductReturns['returnPurchases'] : $purchaseProductReturns);

        if (!empty($purchaseProductReturns['summary'])) {
            $purchaseProductReturnResources->additional(['summary' => $purchaseProductReturns['summary']]);
        }

        return  $purchaseProductReturnResources;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(StoreRequest $request): AnonymousResourceCollection
    {
        $purchaseProductReturns = $this->purchaseProductReturnRepository->save($request->all());

        return PurchaseProductReturnResource::collection($purchaseProductReturns);
    }

    /**
     * Display the specified resource.
     *
     * @param PurchaseProductReturn $purchaseProductReturn
     * @return PurchaseProductReturnResource
     */
    public function show(PurchaseProductReturn $purchaseProductReturn)
    {
        return new PurchaseProductReturnResource($purchaseProductReturn);
    }
}
