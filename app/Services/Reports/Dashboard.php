<?php

namespace App\Services\Reports;

use App\Models\Branch;
use App\Models\DailySummaryReportHistory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductReturn;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseProductReturn;
use App\Repositories\Contracts\ExpenseRepository;
use App\Repositories\Contracts\IncomeRepository;
use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Dashboard
{
    /**
     * get resident access request states
     *
     * @param $searchCriteria
     */
    public static function dashboardCountOfModelInstanceStates($searchCriteria)
    {
        $orderStatus = "delivered";
        $purchaseStatus = "received";
        $filterBranch = isset($searchCriteria['branchId']) ? 'branchId' . '=' . $searchCriteria['branchId'] : '1 = 1 ';

        if ($searchCriteria['dataOf'] == 'all') {
            $dateFilter = ' 1 = 1';
        } else if ($searchCriteria['dataOf'] == 'today') {
            $startDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $dateFilter = sprintf(" Date(created_at) BETWEEN '%s' AND '%s'", $startDate, $endDate);
        } else if ($searchCriteria['dataOf'] == 'this_week') {
            $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $dateFilter = sprintf(" Date(created_at) BETWEEN '%s' AND '%s'", $startDate, $endDate);
        } else if ($searchCriteria['dataOf'] == 'this_month') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $dateFilter = sprintf(" Date(created_at) BETWEEN '%s' AND '%s'", $startDate, $endDate);
        } else if ($searchCriteria['dataOf'] == 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
            $dateFilter = sprintf(" Date(created_at) BETWEEN '%s' AND '%s'", $startDate, $endDate);
        } else {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $dateFilter = sprintf(" Date(created_at) BETWEEN '%s' AND '%s'", $startDate, $endDate);
        }

        $totalCustomers = "(SELECT COUNT(*) FROM customers) as totalCustomers";
        $totalProducts = "(SELECT COUNT(*) FROM products) as totalProducts";
        $totalSuppliers = "(SELECT COUNT(*) FROM suppliers) as totalSuppliers";
        $totalOrders = "(SELECT COUNT(*) FROM orders WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalOrders";
        $totalPurchases = "(SELECT COUNT(*) FROM purchases WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalPurchases";
        $totalSaleInAmount = "(SELECT SUM(amount) FROM orders WHERE status='$orderStatus' AND " . $filterBranch . " AND " . $dateFilter . ") as totalSaleInAmount";
        $totalPurchaseInAmount = "(SELECT SUM(totalAmount) FROM purchases WHERE status='$purchaseStatus' AND " . $filterBranch . " AND " . $dateFilter . ") as totalPurchaseInAmount";
        $totalReturnSaleInAmount = "(SELECT SUM(returnAmount) FROM order_product_returns WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalReturnSaleInAmount";
        $totalExpenseInAmount = "(SELECT SUM(amount) FROM expenses WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalExpenseInAmount";
        $totalCustomerDueInAmount = "(SELECT SUM(due) FROM orders WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalCustomerDueInAmount";

        $result = DB::select("SELECT
            $totalCustomers,
            $totalOrders,
            $totalSuppliers,
            $totalPurchases,
            $totalProducts,
            $totalSaleInAmount,
            $totalPurchaseInAmount,
            $totalReturnSaleInAmount,
            $totalExpenseInAmount,
            $totalCustomerDueInAmount
        ");

        return $result[0];
    }

    public static function getMonthlySaleCount($searchCriteria): array
    {
        return [
            'monthlyReport' => ['labels' => self::monthDates($searchCriteria)['formatDates'], 'data' => self::monthDaysSale($searchCriteria)],
            'yearlyReport' => self::yearlyMonthWiseSalesOrders($searchCriteria)
        ];
    }

    /**
     * @param $searchCriteria
     * @return array[]
     */
    public static function monthDates($searchCriteria): array
    {
        $month = isset($searchCriteria['month']) ? $searchCriteria['month'] : Carbon::now()->format('Y-m');
        $monthStart = Carbon::parse($month)->startOfMonth();
        $monthTotalDays = Carbon::parse($month)->daysInMonth;
        $dates = [];
        $formatDates = [];
        for ($i = 0; $i < $monthTotalDays; $i++) {
            $dates[] = $monthStart->format('Y-m-d');
            $formatDates[] = $monthStart->format('d M');
            $monthStart->addDay();
        }
        return ['dates' => $dates, 'formatDates' => $formatDates];
    }

    /**
     * @param $searchCriteria
     * @return array
     */
    public static function monthDaysSale($searchCriteria): array
    {
        $orderProductRepo = app(OrderProductRepository::class);
        $orderProductModel = $orderProductRepo->getModel();
        $month = isset($searchCriteria['month']) ? $searchCriteria['month'] : Carbon::now()->format('Y-m');
        $monthDates = self::monthDates($searchCriteria)['dates'];
        $currentMonthFirstDate = Carbon::parse($month)->startOfMonth()->format('Y-m-d');
        $currentMonthLastDate = Carbon::parse($month)->endOfMonth()->format('Y-m-d');
        $orderProduct = $orderProductModel->whereBetween('date', [$currentMonthFirstDate, $currentMonthLastDate])->get(['id', 'date', 'quantity']);
        $dateQuantities = [];
        foreach ($monthDates as $date) {
            $totalQuantities = $orderProduct->where('date', $date)->sum('quantity');
            $dateQuantities[] = (int)$totalQuantities;
        }
        return $dateQuantities;
    }

    public static function yearlyMonthWiseSalesOrders($searchCriteria): array
    {
        $orderRepo = app(OrderRepository::class);
        $orderModel = $orderRepo->getModel();
        $year = isset($searchCriteria['year']) ? Carbon::createFromDate($searchCriteria['year'])->format('Y') : Carbon::now()->format('Y');
        $yearStartDate = Carbon::createFromDate($year)->startOfYear()->format('Y-m-d');
        $yearEndDate = Carbon::createFromDate($year)->endOfYear()->format('Y-m-d');
        $yearlySales = $orderModel->whereBetween('date', [$yearStartDate, $yearEndDate])->get(['id', 'date', 'amount']);

        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July ', 'August', 'September', 'October', 'November', 'December'];
        $monthTotalOrders = [];
        $monthTotalSales = [];
        foreach ($months as $month) {
            $yearMonth = Carbon::createFromDate($year, Carbon::parse($month)->format('m'));
            $monthStartDate = $yearMonth->startOfMonth()->format('Y-m-d');
            $monthEndDate = $yearMonth->endOfMonth()->format('Y-m-d');
            $monthOrders = $yearlySales->where('date', '>=', $monthStartDate)->where('date', '<=', $monthEndDate);
            $monthTotalOrders[] = $monthOrders->count();
            $monthTotalSales[] = $monthOrders->sum('amount');
        }
        return ['month' => $months, 'totalOrder' => $monthTotalOrders, 'totalSale' => $monthTotalSales];
    }

    /**
     * @param $searchCriteria
     * @return array
     * @throws ValidationException
     */
    public static function getDailySummary($searchCriteria): array
    {
        $dailySummaryReportHistory = DailySummaryReportHistory::query()
            ->where('date', $searchCriteria['date'])
            ->first();

        if ($dailySummaryReportHistory) {
            $branchId = $searchCriteria['branchId'] ?? null;
            return $branchId ? $dailySummaryReportHistory->branch_wise[$branchId] : $dailySummaryReportHistory->all_branch;
        }

        $date = $searchCriteria['date'] ?? Carbon::now()->format('Y-m-d');
        $filterBranch = isset($searchCriteria['branchId']) ? 'branchId' . '=' . $searchCriteria['branchId'] : '1 = 1 ';

        $expenseRepo = app(ExpenseRepository::class);
        $expenseModelTable = $expenseRepo->getModel()->getTable();

        $incomeRepo = app(IncomeRepository::class);
        $incomeModelTable = $incomeRepo->getModel()->getTable();

        $expenseFilterBranch = isset($searchCriteria['branchId']) ? 'expenses.branchId' . '=' . $searchCriteria['branchId'] : '1 = 1 ';
        $incomeFilterBranch = isset($searchCriteria['branchId']) ? 'incomes.branchId' . '=' . $searchCriteria['branchId'] : '1 = 1 ';

        if (isset($searchCriteria['startDate']) && isset($searchCriteria['endDate'])) {
            $dateFilter = sprintf(" Date(created_at) >= '%s' and Date(created_at) <= '%s'", $searchCriteria['startDate'], $searchCriteria['endDate']);
            $expenseDateFilter = sprintf(" Date(expenses.created_at) >= '%s' and Date(expenses.created_at) <= '%s'", $searchCriteria['startDate'], $searchCriteria['endDate']);
            $incomeDateFilter = sprintf(" Date(incomes.created_at) >= '%s' and Date(incomes.created_at) <= '%s'", $searchCriteria['startDate'], $searchCriteria['endDate']);
        } else {
            $dateFilter = sprintf(" Date(created_at) = '%s'", $date);
            $expenseDateFilter = sprintf(" Date(expenses.created_at) = '%s'", $date);
            $incomeDateFilter = sprintf(" Date(incomes.created_at) = '%s'", $date);
        }

        $totalSaleInAmount = "(SELECT SUM(amount) FROM orders WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalSaleInAmount";
        $totalReturnSaleInAmount = "(SELECT SUM(returnAmount) FROM order_product_returns WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalReturnSaleInAmount";
        $totalPurchaseInAmount = "(SELECT SUM(totalAmount) FROM purchases WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalPurchaseInAmount";
        $totalReturnPurchaseInAmount = "(SELECT SUM(returnAmount) FROM purchase_product_returns WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalReturnPurchaseInAmount";
        $totalExpenseInAmount = "(SELECT SUM(amount) FROM expenses WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalExpenseInAmount";
        $totalIncomeInAmount = "(SELECT SUM(amount) FROM incomes WHERE " . $filterBranch . " AND " . $dateFilter . ") as totalIncomeInAmount";
        $totalSaleDueAmount = "(SELECT SUM(due) FROM orders WHERE due > 0 AND " . $filterBranch . " AND " . $dateFilter . ") as totalSaleDueAmount";
        $totalPurchaseDueAmount = "(SELECT SUM(due) FROM purchases WHERE due > 0 AND " . $filterBranch . " AND " . $dateFilter . ") as totalPurchaseDueAmount";

        $totalExpenseInAmountWithCategoryName = DB::table($expenseModelTable)
            ->join('expense_categories', 'expenses.categoryId', '=', 'expense_categories.id')
            ->select('expenses.amount', 'expense_categories.name')
            ->whereRaw($expenseFilterBranch)
            ->whereRaw($expenseDateFilter)
            ->get();

        $totalIncomeInAmountWithCategoryName = DB::table($incomeModelTable)
            ->join('income_categories', 'incomes.categoryId', '=', 'income_categories.id')
            ->select('incomes.amount', 'income_categories.name')
            ->whereRaw($incomeFilterBranch)
            ->whereRaw($incomeDateFilter)
            ->get();

        $totalSaleDuePayment = Payment::query()
            ->when(isset($searchCriteria['branchId']), function ($query) use ($searchCriteria) {
                $query->whereHas('paymentable', fn($q) => $q->where('branchId', $searchCriteria['branchId']));
            })
            ->whereHas('paymentable', fn($q) => $q->whereDate('created_at', '!=', $date))
            ->whereRaw($dateFilter)
            ->where('payType', Payment::PAY_TYPE_ORDER_DUE)
            ->sum('amount');

        $todayTotalSaleDuePayment = Payment::query()
            ->when(isset($searchCriteria['branchId']), function ($query) use ($searchCriteria) {
                $query->whereHas('paymentable', fn($q) => $q->where('branchId', $searchCriteria['branchId']));
            })
            ->whereHas('paymentable', fn($q) => $q->whereDate('created_at', '=', $date))
            ->whereRaw($dateFilter)
            ->where('payType', Payment::PAY_TYPE_ORDER_DUE)
            ->sum('amount');

        $totalPurchaseDuePayment = Payment::query()
            ->when(isset($searchCriteria['branchId']), function ($query) use ($searchCriteria) {
                $query->whereHas('paymentable', fn($q) => $q->where('branchId', $searchCriteria['branchId']));
            })
            ->whereRaw($dateFilter)
            ->where('payType', Payment::PAY_TYPE_PURCHASE_DUE)
            ->sum('amount');

        $totalPurchaseReturnAmount = Payment::query()
            ->when(isset($searchCriteria['branchId']), function ($query) use ($searchCriteria) {
                $query->whereHas('paymentable', fn($q) => $q->where('branchId', $searchCriteria['branchId']));
            })
            ->whereRaw($dateFilter)
            ->where('paymentableType', PurchaseProductReturn::class)
            ->sum('amount');

        $totalSaleReturnAmount = Payment::query()
            ->when(isset($searchCriteria['branchId']), function ($query) use ($searchCriteria) {
                $query->whereHas('paymentable', fn($q) => $q->where('branchId', $searchCriteria['branchId']));
            })
            ->whereRaw($dateFilter)
            ->where('paymentableType', OrderProductReturn::class)
            ->sum('amount');


        $result = DB::select("SELECT
              $totalSaleInAmount,
              $totalReturnSaleInAmount,
              $totalPurchaseInAmount,
              $totalReturnPurchaseInAmount,
              $totalExpenseInAmount,
              $totalIncomeInAmount,
              $totalSaleDueAmount,
              $totalPurchaseDueAmount
        ");

        $paymentMethods = Payment::getConstantsByPrefix('METHOD_');

        $salesPaymentMethodAmount = [];
        $purchasePaymentMethodAmount = [];
        foreach ($paymentMethods as $paymentMethod) {
            $salesPaymentMethodAmount[$paymentMethod] = Payment::query()
                ->when(isset($searchCriteria['branchId']), function ($query) use ($searchCriteria) {
                    $query->whereHas('paymentable', fn($q) => $q->where('branchId', $searchCriteria['branchId']));
                })
                ->whereRaw($dateFilter)
                ->where('paymentableType', Order::class)
                ->where('method', $paymentMethod)
                ->sum('amount');

            $purchasePaymentMethodAmount[$paymentMethod] = Payment::query()
                ->when(isset($searchCriteria['branchId']), function ($query) use ($searchCriteria) {
                    $query->whereHas('paymentable', fn($q) => $q->where('branchId', $searchCriteria['branchId']));
                })
                ->whereRaw($dateFilter)
                ->where('paymentableType', Purchase::class)
                ->where('method', $paymentMethod)
                ->sum('amount');
        }

        return [
            'expenseWithCategory' => $totalExpenseInAmountWithCategoryName,
            'incomeWithCategory' => $totalIncomeInAmountWithCategoryName,
            'salesPaymentMethodAmount' => $salesPaymentMethodAmount,
            'purchasePaymentMethodAmount' => $purchasePaymentMethodAmount,
            'summary' => [
                "totalSaleInAmount" => $result[0]->totalSaleInAmount,
                "totalReturnSaleInAmount" => $result[0]->totalReturnSaleInAmount,
                "totalPurchaseInAmount" => $result[0]->totalPurchaseInAmount,
                "totalReturnPurchaseInAmount" => $result[0]->totalReturnPurchaseInAmount,
                "totalExpenseInAmount" => $result[0]->totalExpenseInAmount,
                "totalIncomeInAmount" => $result[0]->totalIncomeInAmount,
                "totalSaleDueAmount" => $result[0]->totalSaleDueAmount,
                "totalPurchaseDueAmount" => $result[0]->totalPurchaseDueAmount,
                "totalSaleDuePaymentAmount" => $totalSaleDuePayment,
                "todayTotalSaleDuePaymentAmount" => $todayTotalSaleDuePayment,
                "totalPurchaseDuePaymentAmount" => $totalPurchaseDuePayment,
                "totalSaleReturnAmount" => $totalSaleReturnAmount,
                "totalPurchaseReturnAmount" => $totalPurchaseReturnAmount,
            ]
        ];

    }

    /**
     * @return void
     */
    public static function storeDailySummary()
    {
        $date = Carbon::now()->subDay()->format('Y-m-d');
        $data = ['date' => $date];

        try {
            $branches = Branch::query()
                ->where('status', Branch::STATUS_ACTIVE)
                ->pluck('id');

            $branchWiseDailySummary = $branches->mapWithKeys(function ($branchId) use ($data) {
                $data['branchId'] = $branchId;
                $dailySummary = self::getDailySummary($data);
                unset($data['branchId']);
                return [$branchId => $dailySummary];
            });

            $allBranchDailySummary = self::getDailySummary($data);

            DailySummaryReportHistory::updateOrCreate(
                ['date' => $data['date']],
                [
                    'branch_wise' => $branchWiseDailySummary,
                    'all_branch' => $allBranchDailySummary,
                    'operation_status' => true,
                    'operation_message' => 'Daily summary report history store successful.',
                ]
            );
        } catch (\Exception $exception){
            DailySummaryReportHistory::updateOrCreate(
                ['date' => $data['date']],
                [
                    'branch_wise' => [],
                    'all_branch' => [],
                    'operation_status' => false,
                    'operation_message' => $exception->getMessage(),
                ]
            );
        }
    }

}
