<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\GroupByStockRequest;
use App\Http\Requests\Stock\IndexRequest;
use App\Http\Requests\Stock\StoreRequest;
use App\Http\Requests\Stock\UpdateComboRequest;
use App\Http\Requests\Stock\UpdateRequest;
use App\Http\Resources\ProductGroupByStockResource;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class StockController extends Controller
{
    /**
     * @var StockRepository
     */
    protected $stockRepository;

    /**
     * StockController constructor.
     * @param StockRepository $stockRepository
     */
    public function __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $stocks = $this->stockRepository->findBy($request->all());

        return StockResource::collection($stocks);
    }

    /**
     * Display a listing of the resource.
     *
     * @param GroupByStockRequest $request
     * @return AnonymousResourceCollection
     */
    public function getProductGroupByStock(GroupByStockRequest $request): AnonymousResourceCollection
    {
        $stocks = $this->stockRepository->getProductGroupByStock($request->all());

        return ProductGroupByStockResource::collection($stocks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return StockResource
     */
    public function store(StoreRequest $request)
    {
        $stock = $this->stockRepository->save($request->all());

        return new StockResource($stock);
    }

    /**
     * Display the specified resource.
     *
     * @param Stock $stock
     * @return StockResource
     */
    public function show(Stock $stock)
    {
        return new StockResource($stock);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Stock $stock
     * @return StockResource
     */
    public function update(UpdateRequest $request, Stock $stock): StockResource
    {
        $stocks = $this->stockRepository->getModel()
            ->where('sku', $stock->sku)
            ->where('productId', $stock->productId)
            ->where('unitCost', $stock->unitCost)
            ->where('unitPrice', $stock->unitPrice)
            ->where('expiredDate', $stock->expiredDate)
            ->get();

        $stocks->each(function ($stock) use ($request) {
            $this->stockRepository->update($stock, $request->all());
        });

        return new StockResource($stock);
    }


    /**
     * @param UpdateComboRequest $request
     * @param Stock $stock
     * @return StockResource
     * @throws ValidationException
     */
    public function updateComboProduct(UpdateComboRequest $request, Stock $stock): StockResource
    {
        $stock = $this->stockRepository->updateCombProduct($stock, $request->all());

        return new StockResource($stock);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Stock $stock
     * @return JsonResponse
     */
    public function destroy(Stock $stock)
    {
        $this->stockRepository->delete($stock);

        return \response()->json(null, 204);
    }

    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function getArchiveData(IndexRequest $request): AnonymousResourceCollection
    {
        $stocks = $this->stockRepository->findBy($request->all(), false, true);

        return StockResource::collection($stocks);
    }

    /**
     * @return JsonResponse
     */
    public function mergeSameSkuStock(): JsonResponse
    {
        return $this->stockRepository->mergeStock();
    }
}
