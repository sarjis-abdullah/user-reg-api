<?php

namespace App\Http\Controllers\Woocommerce;

use App\Events\Woocommerce\BrandSavingEvent;
use App\Events\Woocommerce\CategorySavingEvent;
use App\Events\Woocommerce\ProductSavingEvent;
use App\Events\Woocommerce\SubCategorySavingEvent;
use App\Events\Woocommerce\TaxSavingEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Request;
use App\Models\Branch;
use App\Repositories\Contracts\ProductRepository;
use Illuminate\Http\JsonResponse;

class WoocommerceResourceController extends Controller
{
    /**
     * @return JsonResponse|void
     */
    protected function hasEcomBranchExists()
    {
        return Branch::where('type', Branch::TYPE_ECOMMERCE)->first()->exists();
    }

    /**
     * @return JsonResponse
     */
    public function uploadSystemCategoriesToWC(): JsonResponse
    {
        if(!self::hasEcomBranchExists()) {
            return response()->json('No Ecommerce Branch Found!', 404);
        }

        event(new CategorySavingEvent('batch-create'));

        return response()->json(['message' => 'Categories successfully sync with woocommerce']);
    }

    /**
     * @return JsonResponse
     */
    public function uploadSystemSubCategoriesToWC(): JsonResponse
    {
        if(!self::hasEcomBranchExists()) {
            return response()->json('No Ecommerce Branch Found!', 404);
        }

        event(new SubCategorySavingEvent('batch-create'));

        return response()->json(['message' => 'Categories successfully sync with woocommerce']);
    }

    /**
     * @return JsonResponse
     */
    public function uploadSystemBrandsToWC(): JsonResponse
    {
        if(!self::hasEcomBranchExists()) {
            return response()->json('No Ecommerce Branch Found!', 404);
        }

        event(new BrandSavingEvent('batch-create'));

        return response()->json(['message' => 'Brands successfully sync with woocommerce']);
    }

    /**
     * @return JsonResponse
     */
    public function uploadSystemTaxesToWC(): JsonResponse
    {
        if(!self::hasEcomBranchExists()) {
            return response()->json('No Ecommerce Branch Found!', 404);
        }

        event(new TaxSavingEvent('batch-create'));

        return response()->json(['message' => 'Taxes successfully sync with woocommerce']);
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function uploadSystemProductsToWC(IndexRequest $request): JsonResponse
    {
        $branch = Branch::where('type', Branch::TYPE_ECOMMERCE)->first();

        if(!$branch instanceof Branch) {
            return response()->json('No Ecommerce Branch Found!', 404);
        }

        $products = app(ProductRepository::class)->findBy($request->all())['products'];

        $products->each(function ($product) use ($branch) {
            sleep(1);
            event(new ProductSavingEvent('saved', $product, $branch));
        });

        return response()->json(['message' => 'Products successfully sync with woocommerce']);
    }
}
