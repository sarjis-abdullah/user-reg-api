<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Category;
use App\Models\OrderProductReturn;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DBQueryUpdateController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function executeQuery(Request $request): JsonResponse
    {
        $this->authorize('store', Admin::class);

        $request->validate([
            'sql' => 'required|string'
        ]);

        try {

            if ($request->filled('sql')) {
                DB::transaction(function () use ($request) {
                    DB::statement($request->get('sql'));
                });
            }

            return response()->json([
                'success' => 'Update successful.',
                'code' => '200'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function removeDuplicateCategory(): JsonResponse
    {
        try {

            DB::transaction(function () {

                $categories = ['Medicine', 'Toiletries', 'Surgical'];

                $keepCategory = [];
                foreach ($categories as $category) {
                    $dbCategory = Category::query()->where('name', $category)->first();
                    array_merge($keepCategory, [$category => $dbCategory->id]);
                    $keepCategory[$category] = $dbCategory->id;
                }

                foreach ($categories as $category) {
                    Product::query()
                        ->whereHas('category', function ($q) use ($category) {
                            $q->where('name', $category);
                        })
                        ->update(['categoryId' => $keepCategory[$category]]);
                }

                Category::query()->whereNotIn('id', array_values($keepCategory))->delete();
            });

            return response()->json([
                'success' => 'Category Updated successful.',
                'code' => '200'
            ]);

        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function updateOrderReturnProductId(): JsonResponse
    {
        try {

            $orderProductReturns = OrderProductReturn::query()
                ->with('orderProduct:id,productId,orderId,stockId')
                ->get([
                    'id',
                    'orderProductId',
                    'productId',
                    'stockId'
                ]);

            foreach ($orderProductReturns as $orderProductReturn){
                $orderProductReturn->update([
                    'productId' => $orderProductReturn->orderProduct->productId,
                    'stockId' => $orderProductReturn->orderProduct->stockId
                ]);
            }

            return response()->json([
                'success' => 'Order Product Return Updated successful.',
                'code' => '200'
            ]);

        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]);
        }
    }
}
