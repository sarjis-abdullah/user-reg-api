<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductReturn;
use App\Models\Stock;
use App\Repositories\Contracts\BranchRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderProductReturnRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\PurchaseProductRepository;
use App\Repositories\Contracts\PurchaseProductReturnRepository;
use App\Repositories\Contracts\PurchaseRepository;
use App\Repositories\Contracts\StockRepository;
use App\Repositories\Contracts\SupplierRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class Profit
{
    /**
     * @param $searchCriteria
     * @return array
     */
    public static function getDateWiseSaleProfit($searchCriteria): array
    {
        $orderRepository = app(OrderRepository::class);
        $orderProductReturnRepository = app(OrderProductReturnRepository::class);
        $thisModelTable = $orderRepository->getModel()->getTable();
        $oprModelTable = $orderProductReturnRepository->getModel()->getTable();

        $filterBranch = isset($searchCriteria['branchId']) ? $thisModelTable . '.branchId' . '=' . $searchCriteria['branchId'] : '1 = 1';
        $oprFilterBranch = isset($searchCriteria['branchId']) ? $oprModelTable . '.branchId' . '=' . $searchCriteria['branchId'] : '1 = 1';
        $filterCustomer = isset($searchCriteria['customerId']) ? $thisModelTable . '.customerId' . '=' . $searchCriteria['customerId'] : '1 = 1';

        if ((isset($searchCriteria['endYear']) || isset($searchCriteria['startYear'])) && empty($searchCriteria['startMonth'])) {
            $selectQuery = 'YEAR(' . $thisModelTable . '.created_at) as year';
            $selectOPRQuery = 'YEAR(' . $oprModelTable . '.created_at) as year';
        } else if (isset($searchCriteria['startMonth']) || isset($searchCriteria['endMonth'])) {
            $selectQuery = 'YEAR(' . $thisModelTable . '.created_at) as year, MONTH(' . $thisModelTable . '.created_at) as monthNumber, DATE_FORMAT(' . $thisModelTable . '.created_at, "%M") as month';
            $selectOPRQuery = 'YEAR(' . $oprModelTable . '.created_at) as year, MONTH(' . $oprModelTable . '.created_at) as monthNumber, DATE_FORMAT(' . $oprModelTable . '.created_at, "%M") as month';
        } else {
            $selectQuery = 'DATE_FORMAT(' . $thisModelTable . '.created_at, "%d/%m/%Y") as date';
            $selectOPRQuery = 'DATE_FORMAT(' . $oprModelTable . '.created_at, "%d/%m/%Y") as date';
        }

        $endDate = isset($searchCriteria['endDate']) ? Carbon::parse($searchCriteria['endDate'])->endOfDay() : Carbon::now();
        $startDate = isset($searchCriteria['startDate']) ? Carbon::parse($searchCriteria['startDate'])->startOfDay() : Carbon::now();

        $endYear = $searchCriteria['endYear'] ?? Carbon::now()->year;
        $startYear = $searchCriteria['startYear'] ?? Carbon::now()->year;

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;

        $ordersQueryBuilder = DB::table($thisModelTable)
            ->select(
                DB::raw($selectQuery),
                DB::raw('count(*) as totalSales'),
                DB::raw('SUM(' . $thisModelTable . '.amount) as totalSaleAmount'),
                DB::raw('SUM(' . $thisModelTable . '.paid) as totalPaidAmount'),
                DB::raw('SUM(' . $thisModelTable . '.due) as totalDueAmount'),
                DB::raw('SUM(' . $thisModelTable . '.discount) as totalDiscountAmount'),
                DB::raw('SUM(' . $thisModelTable . '.tax) as totalTaxAmount'),
                DB::raw('SUM(' . $thisModelTable . '.shippingCost) as totalShippingCostAmount'),
                DB::raw('SUM(' . $thisModelTable . '.profitAmount) as totalProfitAmount'),
                DB::raw('SUM(' . $thisModelTable . '.grossProfit) as totalGrossProfitAmount'),
                DB::raw('NULL as totalReturnAmount'),
                DB::raw('NULL as totalReturnProfitAmount'),
                DB::raw('NULL as totalReturnDiscountAmount')
            );

        $orderReturnQueryBuilder = DB::table($oprModelTable)
            ->select(
                DB::raw($selectOPRQuery),
                DB::raw('NULL as totalSales'),
                DB::raw('NULL as totalSaleAmount'),
                DB::raw('NULL as totalPaidAmount'),
                DB::raw('NULL as totalDueAmount'),
                DB::raw('NULL as totalDiscountAmount'),
                DB::raw('NULL as totalTaxAmount'),
                DB::raw('NULL as totalShippingCostAmount'),
                DB::raw('NULL as totalProfitAmount'),
                DB::raw('NULL as totalGrossProfitAmount'),
                DB::raw('SUM(' . $oprModelTable . '.returnAmount) as totalReturnAmount'),
                DB::raw('SUM(' . $oprModelTable . '.profitAmount) as totalReturnProfitAmount'),
                DB::raw('SUM(' . $oprModelTable . '.discountAmount) as totalReturnDiscountAmount'),
            );

        if (isset($searchCriteria['startMonth']) || isset($searchCriteria['endMonth'])) {
            $endMonth = $searchCriteria['endMonth'] ?? Carbon::now()->month;
            $startMonth = $searchCriteria['startMonth'] ?? Carbon::now()->month;

            $ordersQueryBuilder = $ordersQueryBuilder
                ->whereBetween($thisModelTable . '.created_at', [
                    Carbon::create($startYear, $startMonth)->startOfMonth()->format('Y-m-d H:i:s'),
                    Carbon::create($endYear, $endMonth)->lastOfMonth()->endOfDay()->format('Y-m-d H:i:s'),
                ])
                ->groupBy('year', 'monthNumber');

            $orderReturnQueryBuilder = $orderReturnQueryBuilder
                ->whereBetween($oprModelTable . '.created_at', [
                    Carbon::create($startYear, $startMonth)->startOfMonth()->format('Y-m-d H:i:s'),
                    Carbon::create($endYear, $endMonth)->lastOfMonth()->endOfDay()->format('Y-m-d H:i:s'),
                ])
                ->groupBy('year', 'monthNumber');


        } else {

            $ordersQueryBuilder = $ordersQueryBuilder
                ->whereDate($thisModelTable . '.created_at', '<=', $endDate)
                ->whereDate($thisModelTable . '.created_at', '>=', $startDate)
                ->groupByRaw('DATE_FORMAT(orders.created_at, "%d/%m/%Y")');

            $orderReturnQueryBuilder = $orderReturnQueryBuilder
                ->whereDate($oprModelTable . '.created_at', '<=', $endDate)
                ->whereDate($oprModelTable . '.created_at', '>=', $startDate)
                ->groupByRaw('DATE_FORMAT(order_product_returns.created_at, "%d/%m/%Y")');

        }

        $ordersQueryBuilder = $ordersQueryBuilder->whereRaw(DB::raw($filterBranch));
        $orderReturnQueryBuilder = $orderReturnQueryBuilder->whereRaw(DB::raw($oprFilterBranch));

        if (isset($searchCriteria['startMonth']) || isset($searchCriteria['endMonth'])) {

            $result = DB::query()
                ->fromSub($ordersQueryBuilder->union($orderReturnQueryBuilder), 'combined_results')
                ->select(
                    'year', 'monthNumber', 'month',
                    DB::raw('SUM(totalSales) AS totalSales'),
                    DB::raw('SUM(totalSaleAmount) AS totalSaleAmount'),
                    DB::raw('SUM(totalPaidAmount) AS totalPaidAmount'),
                    DB::raw('SUM(totalDueAmount) AS totalDueAmount'),
                    DB::raw('SUM(totalDiscountAmount) AS totalDiscountAmount'),
                    DB::raw('SUM(totalTaxAmount) AS totalTaxAmount'),
                    DB::raw('SUM(totalShippingCostAmount) AS totalShippingCostAmount'),
                    DB::raw('SUM(totalProfitAmount) AS totalProfitAmount'),
                    DB::raw('SUM(totalGrossProfitAmount) AS totalGrossProfitAmount'),
                    DB::raw('SUM(totalReturnAmount) AS totalReturnAmount'),
                    DB::raw('SUM(totalReturnProfitAmount) AS totalReturnProfitAmount'),
                    DB::raw('SUM(totalReturnDiscountAmount) AS totalReturnDiscountAmount')
                )
                ->groupBy('year', 'monthNumber');

        }else{
            $result = DB::query()
                ->fromSub($ordersQueryBuilder->union($orderReturnQueryBuilder), 'combined_results')
                ->select(
                    'date',
                    DB::raw('SUM(totalSales) AS totalSales'),
                    DB::raw('SUM(totalSaleAmount) AS totalSaleAmount'),
                    DB::raw('SUM(totalPaidAmount) AS totalPaidAmount'),
                    DB::raw('SUM(totalDueAmount) AS totalDueAmount'),
                    DB::raw('SUM(totalDiscountAmount) AS totalDiscountAmount'),
                    DB::raw('SUM(totalTaxAmount) AS totalTaxAmount'),
                    DB::raw('SUM(totalShippingCostAmount) AS totalShippingCostAmount'),
                    DB::raw('SUM(totalProfitAmount) AS totalProfitAmount'),
                    DB::raw('SUM(totalGrossProfitAmount) AS totalGrossProfitAmount'),
                    DB::raw('SUM(totalReturnAmount) AS totalReturnAmount'),
                    DB::raw('SUM(totalReturnProfitAmount) AS totalReturnProfitAmount'),
                    DB::raw('SUM(totalReturnDiscountAmount) AS totalReturnDiscountAmount')
                )
                ->groupBy('date');
        }

        $allData = $result->get();
        $pageWiseQueryData = $result->paginate($limit);

        $summary = self::getDateWiseSummaryData($allData);
        $pageWiseSummary = self::getDateWiseSummaryData($pageWiseQueryData);

        if (empty($searchCriteria['withoutPagination'])) {
            return ['result' => $pageWiseQueryData, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        } else {
            return ['result' => $allData, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        }
    }

    /**
     * @param $searchCriteria
     * @return array
     */
    public static function getProductWiseSaleProfit($searchCriteria): array
    {
        Cache::flush();

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $endDate = isset($searchCriteria['endDate']) ? Carbon::parse($searchCriteria['endDate'])->endOfDay() : Carbon::now();
        $startDate = isset($searchCriteria['startDate']) ? Carbon::parse($searchCriteria['startDate'])->startOfDay() : Carbon::now();
        $productIds = isset($searchCriteria['productId']) ? explode(',', $searchCriteria['productId']) : [];
        $branchId = isset($searchCriteria['branchId']) ? $searchCriteria['branchId'] : '';

        $productReports = Stock::query()->withTrashed()->with([
                'product.unit',
                'orderProducts' => fn($q) => $q->whereDate('created_at', '<=', $endDate)
                    ->whereDate('created_at', '>=', $startDate)
                    ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('order', fn($q) => $q->where('branchId', $branchId)))
                    ->when(isset($searchCriteria['productId']), fn($q) => $q->whereIn('productId', $productIds))
                    ->when(isset($searchCriteria['categoryId']), fn($q) => $q->whereHas('product', fn($sq) => $sq->where('categoryId', $searchCriteria['categoryId']))),
                'orderProductReturnByStockId' => fn($q) => $q->whereDate('created_at', '<=', $endDate)
                    ->whereDate('created_at', '>=', $startDate)
                    ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('orderById', fn($q) => $q->where('branchId', $branchId)))
                    ->when(isset($searchCriteria['productId']), fn($q) => $q->whereIn('productId', $productIds))
                    ->when(isset($searchCriteria['categoryId']), fn($q) => $q->whereHas('productById', fn($sq) => $sq->where('categoryId', $searchCriteria['categoryId']))),
            ])
            ->when(isset($searchCriteria['productId']), fn($q) => $q->where('productId', $productIds))
            ->when(isset($searchCriteria['branchId']), fn($q) => $q->where('branchId', $searchCriteria['branchId']))
            ->when(isset($searchCriteria['categoryId']),
                fn($q) => $q->whereHas('product', fn($q) => $q->where('categoryId', $searchCriteria['categoryId'])))
            ->whereHas('orderProducts', fn($q) => $q->whereDate('created_at', '<=', $endDate)
                ->whereDate('created_at', '>=', $startDate)
                ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('order', fn($q) => $q->where('branchId', $branchId)))
                ->when(isset($searchCriteria['productId']), fn($q) => $q->whereIn('productId', $productIds))
                ->when(isset($searchCriteria['categoryId']), fn($q) => $q->whereHas('product', fn($sq) => $sq->where('categoryId', $searchCriteria['categoryId']))))
            ->orWhereHas('orderProductReturnByStockId', fn($q) => $q->whereDate('created_at', '<=', $endDate)
                ->whereDate('created_at', '>=', $startDate)
                ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('orderById', fn($q) => $q->where('branchId', $branchId)))
                ->when(isset($searchCriteria['productId']), fn($q) => $q->whereIn('productId', $productIds))
                ->when(isset($searchCriteria['categoryId']), fn($q) => $q->whereHas('productById', fn($sq) => $sq->where('categoryId', $searchCriteria['categoryId']))))
            ->orderBy($orderBy, $orderDirection);

        $stockIds = $productReports->pluck('id')->toArray();
        $productIds = $productReports->pluck('productId')->toArray();

        $orderProduct = OrderProduct::query()
            ->select(
                DB::raw('SUM(quantity) as totalSaleQuantity'),
                DB::raw('SUM(amount) as totalSaleAmount'),
                DB::raw('SUM(discount) as totalDiscountAmount'),
                DB::raw('SUM(tax) as totalTaxAmount'),
                DB::raw('SUM(profitAmount) as totalProfitAmount'),
                DB::raw('SUM(grossProfit) as totalGrossProfitAmount'),
            )
            ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('order', fn($q) => $q->where('branchId', $branchId)))
            ->whereIn('stockId', $stockIds)
            ->whereDate('created_at', '<=', $endDate)
            ->whereDate('created_at', '>=', $startDate)
            ->first();

        $orderProductReturn = OrderProductReturn::query()
            ->select(
                DB::raw('SUM(quantity) as totalReturnQuantity'),
                DB::raw('SUM(returnAmount) as totalReturnAmount'),
                DB::raw('SUM(discountAmount) as totalReturnDiscountAmount'),
                DB::raw('SUM(profitAmount) as totalReturnProfitAmount'),
            )
            ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('orderById', fn($q) => $q->where('branchId', $branchId)))
            ->whereIn('stockId', $stockIds)
            ->whereDate('created_at', '<=', $endDate)
            ->whereDate('created_at', '>=', $startDate)
            ->first();


        $summary = [
            "totalNetSaleQuantity" => round($orderProduct->totalSaleQuantity - $orderProductReturn->totalReturnQuantity, 2),
            "totalSaleQuantity" => round($orderProduct->totalSaleQuantity, 2),
            "totalReturnQuantity" => round($orderProductReturn->totalReturnQuantity, 2),
            "totalSaleAmount" => round($orderProduct->totalSaleAmount, 2),
            "totalReturnAmount" => round($orderProductReturn->totalReturnAmount, 2),
            "totalDiscountAmount" => round($orderProduct->totalDiscountAmount, 2),
            "totalTaxAmount" => round($orderProduct->totalTaxAmount, 2),
            "totalProfitAmount" => round($orderProduct->totalProfitAmount, 2),
            "totalNetProfitAmount" => round(($orderProduct->totalProfitAmount - $orderProductReturn->totalReturnProfitAmount), 2),
            "totalNetGrossProfitAmount" => round(($orderProduct->totalGrossProfitAmount - ($orderProductReturn->totalReturnProfitAmount - $orderProductReturn->totalReturnDiscountAmount)), 2),
            "totalNetTotalAmount" => round(($orderProduct->totalSaleAmount - $orderProductReturn->totalReturnAmount), 2)
        ];


        $paginateData = $productReports->paginate($limit);

        $paginateStockIds = $paginateData->pluck('id')->toArray();
        $paginateProductIds = $paginateData->pluck('productId')->toArray();

        $paginateOrderProduct = OrderProduct::query()
            ->select(
                DB::raw('SUM(quantity) as totalSaleQuantity'),
                DB::raw('SUM(amount) as totalSaleAmount'),
                DB::raw('SUM(discount) as totalDiscountAmount'),
                DB::raw('SUM(tax) as totalTaxAmount'),
                DB::raw('SUM(profitAmount) as totalProfitAmount'),
                DB::raw('SUM(grossProfit) as totalGrossProfitAmount'),
            )
            ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('order', fn($q) => $q->where('branchId', $branchId)))
            ->whereIn('stockId', $paginateStockIds)
            ->whereDate('created_at', '<=', $endDate)
            ->whereDate('created_at', '>=', $startDate)
            ->first();

        $paginateOrderProductReturn = OrderProductReturn::query()
            ->select(
                DB::raw('SUM(quantity) as totalReturnQuantity'),
                DB::raw('SUM(returnAmount) as totalReturnAmount'),
                DB::raw('SUM(discountAmount) as totalReturnDiscountAmount'),
                DB::raw('SUM(profitAmount) as totalReturnProfitAmount'),
            )
            ->when(isset($searchCriteria['branchId']), fn($q) => $q->whereHas('orderById', fn($q) => $q->where('branchId', $branchId)))
            ->whereIn('stockId', $paginateStockIds)
            ->whereDate('created_at', '<=', $endDate)
            ->whereDate('created_at', '>=', $startDate)
            ->first();

        $pageWiseSummary = [
            "totalNetSaleQuantity" => round($paginateOrderProduct->totalSaleQuantity - $paginateOrderProductReturn->totalReturnQuantity, 2),
            "totalSaleQuantity" => round($paginateOrderProduct->totalSaleQuantity, 2),
            "totalReturnQuantity" => round($paginateOrderProductReturn->totalReturnQuantity, 2),
            "totalSaleAmount" => round($paginateOrderProduct->totalSaleAmount, 2),
            "totalReturnAmount" => round($paginateOrderProductReturn->totalReturnAmount, 2),
            "totalDiscountAmount" => round($paginateOrderProduct->totalDiscountAmount, 2),
            "totalTaxAmount" => round($paginateOrderProduct->totalTaxAmount, 2),
            "totalProfitAmount" => round($paginateOrderProduct->totalProfitAmount, 2),
            "totalNetProfitAmount" => round(($paginateOrderProduct->totalProfitAmount - $paginateOrderProductReturn->totalReturnProfitAmount), 2),
            "totalNetGrossProfitAmount" => round(($paginateOrderProduct->totalGrossProfitAmount - ($paginateOrderProductReturn->totalReturnProfitAmount - $paginateOrderProductReturn->totalReturnDiscountAmount)), 2),
            "totalNetTotalAmount" => round(($paginateOrderProduct->totalSaleAmount - $paginateOrderProductReturn->totalReturnAmount), 2)
        ];

        if (empty($searchCriteria['withoutPagination'])) {
            $productReports = $paginateData;
        } else {
            $productReports = $productReports->get();
        }

        return ['result' => $productReports, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
    }


    /**
     * @param $searchCriteria
     * @return array
     */
    public static function getCategoryWiseSaleReport($searchCriteria): array
    {
        $endDate = isset($searchCriteria['endDate']) ? Carbon::parse($searchCriteria['endDate'])->endOfDay()->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $startDate = isset($searchCriteria['startDate']) ? Carbon::parse($searchCriteria['startDate'])->startOfDay()->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $categoryId = isset($searchCriteria['categoryId']) ? $searchCriteria['categoryId'] : '';
        $branchId = isset($searchCriteria['branchId']) ? $searchCriteria['branchId'] : '1=1';
        $orderProductFilterBranch = isset($searchCriteria['branchId']) ? 'orders.branchId' . '=' . $searchCriteria['branchId'] : '1 = 1';
        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;

        $categoryWiseSales = DB::table('products')
            ->select(
                'categories.name as categoryName',
                'categories.id as categoryId',
                DB::raw('COALESCE(SUM(op.quantity), 0) as orderQuantity'),
                DB::raw('COALESCE(SUM(op.amount), 0) as soldAmount'),
                DB::raw('COALESCE(SUM(op.profitAmount), 0) as saleProfitAmount'),
                DB::raw('COALESCE(SUM(op.grossProfit), 0) as saleGrossProfitAmount'),
                DB::raw('COALESCE(SUM(op_return.quantity), 0) as returnQuantity'),
                DB::raw('COALESCE(SUM(op_return.returnAmount), 0) as returnAmount'),
                DB::raw('COALESCE(SUM(op_return.profitAmount), 0) as returnProfitAmount'),
                DB::raw('COALESCE(SUM(op_return.discountAmount), 0) as returnDiscountAmount'),
            )
            ->leftJoin('categories', 'products.categoryId', '=', 'categories.id')
            ->leftJoin(DB::raw('(SELECT order_products.productId, SUM(order_products.quantity) as quantity, SUM(order_products.amount) as amount, SUM(order_products.profitAmount) as profitAmount, SUM(order_products.grossProfit) as grossProfit
                      FROM order_products
                      INNER JOIN orders ON order_products.orderId = orders.id
                      WHERE DATE(order_products.created_at) BETWEEN "'.$startDate.'" AND "'.$endDate.'" AND '.$orderProductFilterBranch.'
                      GROUP BY productId) as op'), 'op.productId', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT order_product_returns.productId, SUM(order_product_returns.quantity) as quantity, SUM(order_product_returns.returnAmount) as returnAmount, SUM(order_product_returns.profitAmount) as profitAmount, SUM(order_product_returns.discountAmount) as discountAmount
                      FROM order_product_returns
                      INNER JOIN orders ON order_product_returns.orderId = orders.id
                      WHERE DATE(order_product_returns.created_at) BETWEEN "'.$startDate.'" AND "'.$endDate.'" AND '.$orderProductFilterBranch.'
                      GROUP BY productId) as op_return'), 'op_return.productId', '=', 'products.id')
            ->where(function ($query) {
                $query->whereNotNull('op.productId')
                    ->orWhereNotNull('op_return.productId');
            })
            ->when(isset($categoryId) && $categoryId != '', function ($q) use ($categoryId){
                $q->where('products.categoryId', '=', $categoryId);
            })
            ->orderBy('categories.id', 'desc')
            ->groupBy('categories.id');

        $allCategoryWiseSalesSum = $categoryWiseSales->get();

        $summary = [
            'totalSaleQuantity' => round($allCategoryWiseSalesSum->sum('orderQuantity'), 2),
            'totalReturnQuantity' => round($allCategoryWiseSalesSum->sum('returnQuantity'), 2),
            'totalSaleAmount' => round($allCategoryWiseSalesSum->sum('soldAmount'), 2),
            'totalReturnAmount' => round($allCategoryWiseSalesSum->sum('returnAmount'), 2),
            'totalNetSaleQuantity' => round($allCategoryWiseSalesSum->sum('orderQuantity') - $allCategoryWiseSalesSum->sum('returnQuantity'), 2),
            'totalProfitAmount' => round($allCategoryWiseSalesSum->sum('saleProfitAmount') - $allCategoryWiseSalesSum->sum('returnProfitAmount'), 2),
            'totalNetTotalAmount' => round($allCategoryWiseSalesSum->sum('soldAmount') - $allCategoryWiseSalesSum->sum('returnAmount'), 2),
            'totalSaleProfitAmount' => round($allCategoryWiseSalesSum->sum('saleProfitAmount'), 2),
            'totalReturnProfitAmount' => round($allCategoryWiseSalesSum->sum('returnProfitAmount'), 2),
            'totalNetGrossProfitAmount' => round($allCategoryWiseSalesSum->sum('saleGrossProfitAmount') - ($allCategoryWiseSalesSum->sum('returnProfitAmount') - $allCategoryWiseSalesSum->sum('returnDiscountAmount')), 2),
        ];

        $paginateCategoryWiseSales = $categoryWiseSales->paginate($limit);

        $pageWiseSummary = [
            'totalSaleQuantity' => round($paginateCategoryWiseSales->sum('orderQuantity'), 2),
            'totalReturnQuantity' => round($paginateCategoryWiseSales->sum('returnQuantity'), 2),
            'totalSaleAmount' => round($paginateCategoryWiseSales->sum('soldAmount'), 2),
            'totalReturnAmount' => round($paginateCategoryWiseSales->sum('returnAmount'), 2),
            'totalNetSaleQuantity' => round($paginateCategoryWiseSales->sum('orderQuantity') - $paginateCategoryWiseSales->sum('returnQuantity'), 2),
            'totalProfitAmount' => round($paginateCategoryWiseSales->sum('saleProfitAmount') - $paginateCategoryWiseSales->sum('returnProfitAmount'), 2),
            'totalNetTotalAmount' => round($paginateCategoryWiseSales->sum('soldAmount') - $paginateCategoryWiseSales->sum('returnAmount'), 2),
            'totalSaleProfitAmount' => round($paginateCategoryWiseSales->sum('saleProfitAmount'), 2),
            'totalReturnProfitAmount' => round($paginateCategoryWiseSales->sum('returnProfitAmount'), 2),
            'totalNetGrossProfitAmount' => round($paginateCategoryWiseSales->sum('saleGrossProfitAmount') - ($paginateCategoryWiseSales->sum('returnProfitAmount') - $paginateCategoryWiseSales->sum('returnDiscountAmount')), 2),
        ];

        if (empty($searchCriteria['withoutPagination'])) {
            $categoryWiseReports = $paginateCategoryWiseSales;
        } else {
            $categoryWiseReports = $categoryWiseSales->get();
        }

        return ['result' => $categoryWiseReports, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];

    }


    /**
     * @param $data
     * @return array
     */
    public static function getDateWiseSummaryData($data): array
    {
        $summaryData = [
            'totalSales' => round($data->sum('totalSales'), 2),
            'totalSaleAmount' => round($data->sum('totalSaleAmount'), 2),
            'totalReturnAmount' => round($data->sum('totalReturnAmount'), 2),
            'totalPaidAmount' => round($data->sum('totalPaidAmount'), 2),
            'totalDueAmount' => round($data->sum('totalDueAmount'), 2),
            'totalDiscountAmount' => round($data->sum('totalDiscountAmount'), 2),
            'totalTaxAmount' => round($data->sum('totalTaxAmount'), 2),
            'totalNetProfitAmount' => round($data->sum('totalGrossProfitAmount'), 2),
            'totalNetGrossProfitAmount' => round(($data->sum('totalGrossProfitAmount') - ($data->sum('totalReturnProfitAmount') - $data->sum('totalReturnDiscountAmount'))), 2),
            'totalNetTotalAmount' => round(($data->sum('totalSaleAmount') - $data->sum('totalReturnAmount')), 2),
            'totalNetPaidAmount' => round(($data->sum('totalPaidAmount') - $data->sum('totalReturnAmount')), 2),
        ];
        return $summaryData;
    }

    /**
     * @param $data
     * @return array
     */
    public static function getProductWiseSummaryData($data)
    {
        return [
            'totalNetSaleQuantity' => (round($data->sum('totalSaleQuantity'), 2) - round($data->sum('totalReturnQuantity'), 2)),
            'totalSaleQuantity' => round($data->sum('totalSaleQuantity'), 2),
            'totalReturnQuantity' => round($data->sum('totalReturnQuantity'), 2),
            'totalSaleAmount' => round($data->sum('totalSaleAmount')),
            'totalReturnAmount' => round($data->sum('totalReturnAmount'), 2),
            'totalDiscountAmount' => round($data->sum('totalDiscountAmount'), 2),
            'totalTaxAmount' => round($data->sum('totalTaxAmount'), 2),
            'totalProfitAmount' => round($data->sum('totalProfitAmount'), 2),
            'totalNetProfitAmount' => round($data->sum('totalGrossProfitAmount'), 2),
            'totalNetGrossProfitAmount' => round(($data->sum('totalGrossProfitAmount') - ($data->sum('totalReturnProfitAmount') - $data->sum('totalReturnDiscountAmount'))), 2),
            'totalNetTotalAmount' => (round($data->sum('totalSaleAmount')) - round($data->sum('totalReturnAmount'), 2)),
        ];

    }

    /**
     * @param $data
     * @return array
     */
    public static function getCategoryWiseSummaryData($data): array
    {
        return [
            'noOfCategory' => round($data->count(), 2),
            'totalSaleQuantity' => round($data->sum('quantity'), 2),
            'totalSaleAmount' => round($data->sum('soldAmount'), 2),
            'totalProfitAmount' => round($data->sum('profitAmount') - $data->sum('returnProfitAmount'), 2),
            'totalReturnAmount' => round($data->sum('returnTotalAmount'), 2),
            'totalNetTotalAmount' => round($data->sum('soldAmount') - $data->sum('returnTotalAmount'), 2),
            'totalSaleProfitAmount' => round($data->sum('profitAmount'), 2),
            'totalReturnProfitAmount' => round($data->sum('returnProfitAmount'), 2),
        ];

    }

    /**
     * @param $searchCriteria
     * @return array
     */
    public static function getSupplierWisePurchaseReport($searchCriteria): array
    {
        $purchaseRepository = app(PurchaseRepository::class);
        $thisModelTable = $purchaseRepository->getModel()->getTable();

        $supplierRepository = app(SupplierRepository::class);
        $supplierModelTable = $supplierRepository->getModel()->getTable();

        $branchRepository = app(BranchRepository::class);
        $branchModelTable = $branchRepository->getModel()->getTable();

        $filterBranch = isset($searchCriteria['branchId']) ? $thisModelTable . '.branchId' . '=' . $searchCriteria['branchId'] : '1 = 1';
        $filterSupplier = isset($searchCriteria['supplierId']) ? $thisModelTable . '.supplierId' . '=' . $searchCriteria['supplierId'] : '1 = 1';

        $groupBySupplierId = $thisModelTable . '.supplierId';
        $groupByBranchId = $thisModelTable . '.branchId';

        $startDate = isset($searchCriteria['startDate']) ? Carbon::parse($searchCriteria['startDate'])->startOfDay()->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $endDate = isset($searchCriteria['endDate']) ? Carbon::parse($searchCriteria['endDate'])->endOfDay()->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;

        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : $thisModelTable . '.supplierId';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';

        $supplierWisePurchaseReports = DB::table($thisModelTable)
            ->select(
                DB::raw('count(*) as totalPurchase'),
                DB::raw($thisModelTable . '.supplierId as supplierId'),
                DB::raw($branchModelTable . '.id as branchId'),
                DB::raw($branchModelTable . '.name as branchName'),
                DB::raw($supplierModelTable . '.name as supplierName'),
                DB::raw('SUM(' . $thisModelTable . '.totalAmount) as totalPurchaseAmount'),
                DB::raw('SUM(' . $thisModelTable . '.discountAmount) as totalDiscountAmount'),
                DB::raw('SUM(' . $thisModelTable . '.taxAmount) as totalTaxAmount'),
                DB::raw('SUM(' . $thisModelTable . '.paid) as totalPaidAmount'),
                DB::raw('SUM(' . $thisModelTable . '.returnedAmount) as totalReturnAmount'),
                DB::raw('SUM(' . $thisModelTable . '.due) as totalDueAmount')
            )
            ->leftJoin($supplierModelTable, $thisModelTable . '.supplierId', '=', $supplierModelTable . '.id')
            ->leftJoin($branchModelTable, $thisModelTable . '.branchId', '=', $branchModelTable . '.id')
            ->whereRaw(DB::raw($filterBranch))
            ->whereRaw(DB::raw($filterSupplier))
            ->when(isset($startDate) && isset($endDate), function ($query) use ($thisModelTable, $searchCriteria, $startDate, $endDate) {
                $query->whereDate($thisModelTable . '.created_at', '>=', $startDate)->whereDate($thisModelTable . '.created_at', '<=', $endDate);
            })
            ->orderBy($orderBy, $orderDirection)
            ->groupBy($groupBySupplierId, $groupByBranchId);

        if (empty($searchCriteria['withoutPagination'])) {
            $supplierWisePurchaseReports = $supplierWisePurchaseReports->paginate($limit);
        } else {
            $supplierWisePurchaseReports = $supplierWisePurchaseReports->get();
        }

        return ['result' => $supplierWisePurchaseReports];
    }

    /**
     * @param $searchCriteria
     * @return array
     */
    public static function getSupplierWiseStockReport($searchCriteria): array
    {
        $purchaseRepository = app(PurchaseRepository::class);
        $thisModelTable = $purchaseRepository->getModel()->getTable();

        $purchaseProductRepository = app(PurchaseProductRepository::class);
        $purchaseProductModelTable = $purchaseProductRepository->getModel()->getTable();

        $purchaseProductReturnRepository = app(PurchaseProductReturnRepository::class);
        $purchaseProductReturnModelTable = $purchaseProductReturnRepository->getModel()->getTable();

        $orderProductRepository = app(OrderProductRepository::class);
        $orderProductModelTable = $orderProductRepository->getModel()->getTable();

        $orderProductReturnRepository = app(OrderProductReturnRepository::class);
        $orderProductReturnModelTable = $orderProductReturnRepository->getModel()->getTable();

        $stockRepository = app(StockRepository::class);
        $stockModelTable = $stockRepository->getModel()->getTable();

        $supplierRepository = app(SupplierRepository::class);
        $supplierModelTable = $supplierRepository->getModel()->getTable();

        $filterSupplier = isset($searchCriteria['supplierId']) ? $thisModelTable . '.supplierId' . '=' . $searchCriteria['supplierId'] : '1 = 1';

        $groupBySupplierId = $thisModelTable . '.supplierId';

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;

        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : $thisModelTable . '.supplierId';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';

        $supplierWisePurchaseReports = DB::table($thisModelTable)
            ->select(
                DB::raw($thisModelTable . '.supplierId as supplierId'),
                DB::raw($supplierModelTable . '.name as supplierName'),
                DB::raw('SUM(' . $purchaseProductModelTable . '.quantity * ' . $purchaseProductModelTable . '.unitCost) as totalPurchaseCost'),
                DB::raw('SUM(' . $purchaseProductModelTable . '.quantity) as totalPurchaseQuantity'),
                DB::raw('SUM(' . $purchaseProductReturnModelTable . '.returnAmount) as totalPurchaseReturnAmount'),
                DB::raw('SUM(' . $purchaseProductReturnModelTable . '.quantity * ' . $purchaseProductModelTable . '.sellingPrice) as totalPurchaseReturnSellingPrice'),
                DB::raw('SUM(' . $purchaseProductReturnModelTable . '.quantity) as totalPurchaseReturnQuantity'),
                DB::raw('SUM(' . $orderProductModelTable . '.quantity * ' . $stockModelTable . '.unitCost) as totalSellingStockPurchaseCost'),
                DB::raw('SUM(' . $orderProductModelTable . '.quantity * ' . $orderProductModelTable . '.discountedUnitPrice) as totalSellingStockPrice'),
                DB::raw('SUM(' . $orderProductModelTable . '.quantity) as totalSoldQuantity'),
                DB::raw('SUM(' . $orderProductReturnModelTable . '.quantity) as totalSaleReturnQuantity'),
                DB::raw('SUM(' . $orderProductReturnModelTable . '.returnAmount) as totalSaleReturnAmount'),
                DB::raw('SUM(' . $stockModelTable . '.quantity * ' . $stockModelTable . '.unitPrice) as totalLeftStockSellingValue'),
                DB::raw('SUM(' . $stockModelTable . '.quantity * ' . $stockModelTable . '.unitCost) as totalLeftStockPurchaseCost'),
                DB::raw('SUM(' . $stockModelTable . '.quantity) as totalStockLeft'),
            )
            ->leftJoin($supplierModelTable, $thisModelTable . '.supplierId', '=', $supplierModelTable . '.id')
            ->leftJoin($purchaseProductModelTable, $thisModelTable . '.id', '=', $purchaseProductModelTable . '.purchaseId')
            ->leftJoin($purchaseProductReturnModelTable, $thisModelTable . '.id', '=', $purchaseProductReturnModelTable . '.purchaseId')
            ->leftJoin($orderProductModelTable, $purchaseProductModelTable . '.productId', '=', $orderProductModelTable . '.productId')
            ->leftJoin($orderProductReturnModelTable, $orderProductModelTable . '.id', '=', $orderProductReturnModelTable . '.orderProductId')
            ->leftJoin($stockModelTable, $orderProductModelTable . '.productId', '=', $stockModelTable . '.productId')
            ->whereRaw(DB::raw($filterSupplier))
            ->orderBy($orderBy, $orderDirection)
            ->groupBy($groupBySupplierId);

        if (empty($searchCriteria['withoutPagination'])) {
            $supplierWisePurchaseReports = $supplierWisePurchaseReports->paginate($limit);
        } else {
            $supplierWisePurchaseReports = $supplierWisePurchaseReports->get();
        }

        return ['result' => $supplierWisePurchaseReports];

    }


    /**
     * @param $request
     * @return array
     */
    public static function getSalesWiseVatReport($request): array
    {
        $orderRepository = app(OrderRepository::class);
        $orderTable = $orderRepository->getModel()->getTable();

        $customerRepository = app(CustomerRepository::class);
        $customerTable = $customerRepository->getModel()->getTable();

        $limit = $request->get('per_page', 15);

        $orders = DB::table($orderTable)
            ->where('tax', '>', 0)
            ->select(
                DB::raw($orderTable . '.*'),
                DB::raw($customerTable . '.name as customerName'),
            )
            ->leftJoin('customers', 'orders.customerId', '=', 'customers.id')
            ->when($request->filled('customerId'), fn($q) => $q->where($orderTable . '.customerId', $request->customerId))
            ->when($request->filled('invoiceNumber'), fn($q) => $q->where($orderTable . '.invoice', $request->invoiceNumber))
            ->when($request->filled('branchId'), fn($q) => $q->where($orderTable . '.branchId', $request->branchId))
            ->when($request->filled('startDate') && $request->filled('endDate'),
                fn($q) => $q->whereBetween($orderTable . '.date', [$request->startDate, $request->endDate]));

        $summary = [
            'totalSaleAmount' => $orders->sum('amount'),
            'totalTaxAmount' => $orders->sum('tax'),
            'totalDiscountAmount' => $orders->sum('discount'),
            'totalProfitAmount' => $orders->sum('profitAmount'),
            'totalGrossProfit' => $orders->sum('grossProfit'),
            'totalPaidAmount' => $orders->sum('paid'),
            'totalDueAmount' => $orders->sum('due'),
        ];

        $paginateOrders = $orders->paginate($limit);

        $perPageSummary = [
            'totalSaleAmount' => $paginateOrders->sum('amount'),
            'totalTaxAmount' => $paginateOrders->sum('tax'),
            'totalDiscountAmount' => $paginateOrders->sum('discount'),
            'totalProfitAmount' => $paginateOrders->sum('profitAmount'),
            'totalGrossProfit' => $paginateOrders->sum('grossProfit'),
            'totalPaidAmount' => $paginateOrders->sum('paid'),
            'totalDueAmount' => $paginateOrders->sum('due'),
        ];

        if ($request->filled('withoutPagination')) {
            return ['result' => $orders->get(), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        } else {
            return ['result' => $orders->paginate($limit), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        }
    }

    /**
     * @param $request
     * @return array
     */
    public static function getProductWiseVatReport($request): array
    {
        $orderProductRepository = app(OrderProductRepository::class);
        $orderProductTable = $orderProductRepository->getModel()->getTable();

        $productRepository = app(ProductRepository::class);
        $productTable = $productRepository->getModel()->getTable();

        $limit = $request->get('per_page', 15);

        $orderProducts = DB::table($orderProductTable)
            ->where($orderProductTable . '.tax', '>', 0)
            ->select(
                DB::raw($orderProductTable . '.*'),
                DB::raw($productTable . '.name as productName'),
                DB::raw('orders.branchId as orderBranchId'),
            )
            ->leftJoin($productTable, $orderProductTable . '.productId', '=', $productTable . '.id')
            ->join('orders', $orderProductTable . '.orderId', '=', 'orders.id')
            ->when($request->filled('taxId'), fn($q) => $q->where($orderProductTable . '.taxId', $request->taxId))
            ->when($request->filled('startDate') && $request->filled('endDate'),
                fn($q) => $q->whereBetween($orderProductTable . '.date', [$request->startDate, $request->endDate]))
            ->when($request->filled('branchId'), fn($q) => $q->where('orders.branchId', $request->branchId));

        $summary = [
            'totalSaleAmount' => $orderProducts->sum($orderProductTable . '.amount'),
            'totalTaxAmount' => $orderProducts->sum($orderProductTable . '.tax'),
            'totalDiscountAmount' => $orderProducts->sum($orderProductTable . '.discount'),
            'totalProfitAmount' => $orderProducts->sum($orderProductTable . '.profitAmount'),
            'totalGrossProfit' => $orderProducts->sum($orderProductTable . '.grossProfit'),
        ];

        $paginateOrders = $orderProducts->paginate($limit);

        $perPageSummary = [
            'totalSaleAmount' => $paginateOrders->sum('amount'),
            'totalTaxAmount' => $paginateOrders->sum('tax'),
            'totalDiscountAmount' => $paginateOrders->sum('discount'),
            'totalProfitAmount' => $paginateOrders->sum('profitAmount'),
            'totalGrossProfit' => $paginateOrders->sum('grossProfit'),
        ];

        if ($request->filled('withoutPagination')) {
            return ['result' => $orderProducts->get(), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        } else {
            return ['result' => $orderProducts->paginate($limit), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        }
    }

    /**
     * @param $request
     * @return array
     */
    public static function getSalesPersonReport($request): array
    {
        Cache::flush();

        $limit = $request->get('per_page', 15);

        $salesPersonData = Employee::query()->with(['user:id,name', 'order' => function ($q) use ($request) {
            $q->when($request->filled('startDate') && $request->filled('endDate'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->startDate)->whereDate('created_at', '<=', $request->endDate);
            });
        }, 'order.orderReturnAmount'])
            ->when($request->filled('salesPersonId'), fn($q) => $q->where('id', $request->salesPersonId))
            ->when($request->filled('branchId'), fn($q) => $q->where('branchId', $request->branchId));


        /*
        * Summary for all start
        * */

        $salesPersonIds = $salesPersonData
            ->pluck('id')
            ->toArray();

        $orders = Order::query()
            ->whereIn('salePersonId', $salesPersonIds)
            ->when($request->filled('startDate') && $request->filled('endDate'), function ($q) use ($request) {
                $q->whereDate('orders.created_at', '>=', $request->startDate)->whereDate('orders.created_at', '<=', $request->endDate);
            });

        $orderProductIds = OrderProduct::query()
            ->whereIn('orderId', $orders->pluck('id')->toArray())
            ->pluck('id')
            ->toArray();

        $orderProductReturn = OrderProductReturn::query()
            ->whereIn('orderProductId', $orderProductIds);

        $summary = [
            'totalOrderAmount' => round($orders->sum('amount'), 2),
            'totalOrderReturnAmount' => round($orderProductReturn->sum('returnAmount'), 2),
            'totalNetAmount' => round(($orders->sum('amount') - $orderProductReturn->sum('returnAmount')), 2)
        ];

        /*
         * Summary for pagination start
         * */

        $paginateQuery = $salesPersonData->paginate($limit);
        $paginateSalesPersonIds = $paginateQuery->pluck('id');

        $paginateOrders = Order::query()
            ->whereIn('salePersonId', $paginateSalesPersonIds)
            ->when($request->filled('startDate') && $request->filled('endDate'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->startDate)->whereDate('created_at', '<=', $request->endDate);
            });

        $paginateOrderProductIds = OrderProduct::query()
            ->whereIn('orderId', $paginateOrders->pluck('id')->toArray())
            ->pluck('id')
            ->toArray();

        $paginateOrderProductReturn = OrderProductReturn::query()
            ->whereIn('orderProductId', $paginateOrderProductIds);

        $perPageSummary = [
            'totalOrderAmount' => round($paginateOrders->sum('amount'), 2),
            'totalOrderReturnAmount' => round($paginateOrderProductReturn->sum('returnAmount'), 2),
            'totalNetAmount' => round(($paginateOrders->sum('amount') - $paginateOrderProductReturn->sum('returnAmount')), 2)
        ];

        if ($request->filled('withoutPagination')) {
            return ['result' => $salesPersonData->get(), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        } else {
            return ['result' => $salesPersonData->paginate($limit), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        }
    }




    /**
     * @param $request
     * @return array
     */
    public static function getCashierReport($request): array
    {
        Cache::flush();

        $limit = $request->get('per_page', 15);

        $cashierData = Manager::query()->with(['user:id,name', 'order' => function($q) use($request){
            $q->when($request->filled('startDate') && $request->filled('endDate'), function ($q) use ($request) {
                $q->whereDate('orders.created_at', '>=', $request->startDate)->whereDate('orders.created_at', '<=', $request->endDate);
            });
        }])
            ->when($request->filled('cashierId'), fn($q) => $q->where('id', $request->cashierId))
            ->when($request->filled('branchId'), fn($q) => $q->where('branchId', $request->branchId));

        /*
        * Summary for all start
        * */

        $cashierIds = $cashierData
            ->pluck('userId')
            ->toArray();

        $orders = Order::query()
            ->whereIn('createdByUserId', $cashierIds)
            ->when($request->filled('startDate') && $request->filled('endDate'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->startDate)->whereDate('created_at', '<=', $request->endDate);
            });

        $orderProductIds = OrderProduct::query()
            ->whereIn('orderId', $orders->pluck('id')->toArray())
            ->pluck('id')
            ->toArray();

        $orderProductReturn = OrderProductReturn::query()
            ->whereIn('orderProductId', $orderProductIds);

        $summary = [
            'totalOrderAmount' => round($orders->sum('amount'), 2),
            'totalOrderReturnAmount' => round($orderProductReturn->sum('returnAmount'), 2),
            'totalNetAmount' => round(($orders->sum('amount') - $orderProductReturn->sum('returnAmount')), 2)
        ];

        /*
         * Summary for pagination start
         * */

        $paginateQuery = $cashierData->paginate($limit);
        $paginateCashierIds = $paginateQuery->pluck('userId');

        $paginateOrders = Order::query()
            ->whereIn('createdByUserId', $paginateCashierIds)
            ->when($request->filled('startDate') && $request->filled('endDate'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->startDate)->whereDate('created_at', '<=', $request->endDate);
            });

        $paginateOrderProductIds = OrderProduct::query()
            ->whereIn('orderId', $paginateOrders->pluck('id')->toArray())
            ->pluck('id')
            ->toArray();

        $paginateOrderProductReturn = OrderProductReturn::query()
            ->whereIn('orderProductId', $paginateOrderProductIds);

        $perPageSummary = [
            'totalOrderAmount' => round($paginateOrders->sum('amount'), 2),
            'totalOrderReturnAmount' => round($paginateOrderProductReturn->sum('returnAmount'), 2),
            'totalNetAmount' => round(($paginateOrders->sum('amount') - $paginateOrderProductReturn->sum('returnAmount')), 2)
        ];

        if ($request->filled('withoutPagination')) {
            return ['result' => $cashierData->get(), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        } else {
            return ['result' => $cashierData->paginate($limit), 'summary' => $summary, 'pageWiseSummary' => $perPageSummary];
        }
    }
}
