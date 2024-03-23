<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PaymentRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class CashUp extends Model
{
    use CommonModelFeatures;

    const STATUS_OPEN = 'open';
    const STATUS_BREAK = 'break';
    const STATUS_CLOSED = 'closed';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'companyId',
        'branchId',
        'openedDate',
        'openedBy',
        'openedCash',
        'cashIn',
        'cashOut',
        'closedCash',
        'closedDate',
        'closedBy',
        'openedNotes',
        'closedNotes',
        'dues',
        'cards',
        'cheques',
        'mBanking',
        'total',
        'status',
        'updatedByUserId',
    ];

    /**
     * get the company
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id', 'companyId');
    }

    /**
     * get the branch
     *
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

    public function getExpectedCashBranchWise()
    {

        $queryBranchId = request('branchId') !== null ?? request('branchId');
        $branchId = request('branchId');
        $orderRepo = app(OrderRepository::class);
        $orderModelTable = $orderRepo->getModel()->getTable();


        $paymentRepo = app(PaymentRepository::class);
        $paymentModelTable = $paymentRepo->getModel()->getTable();


        if ($queryBranchId){

            $totalBranchWiseCurrentDaySales = DB::table($orderModelTable)
                ->join($paymentModelTable, $orderModelTable.'.id', '=', $paymentModelTable.'.paymentableId')
                ->where($paymentModelTable.'.paymentableType', '=', Order::class)
                ->where($orderModelTable.'.branchId', '=', $branchId)
                ->where($orderModelTable.'.paymentStatus', '!=', Payment::PAYMENT_STATUS_UNPAID)
                ->whereDate($orderModelTable.'.created_at', Carbon::now())
                ->where($paymentModelTable.'.method', '=', 'cash')
                ->get();

            $totalBranchWiseCurrentDaySalesAmount = $totalBranchWiseCurrentDaySales->sum("amount");

            $totalBranchWiseCurrentDayReturnSales = OrderProductReturn::where('branchId', '=', $branchId)
                ->whereDate('created_at', Carbon::now())
                ->sum('returnAmount');

            $totalBranchWiseCurrentDayExpenses = Expense::where('branchId', '=', $branchId)
                ->whereDate('created_at', Carbon::now())
                ->sum('amount');

            $totalBranchWiseCurrentDayIncomes = Income::where('branchId', '=', $branchId)
                ->whereDate('created_at', Carbon::now())
                ->sum('amount');

            return round(($this->openedCash + $totalBranchWiseCurrentDaySalesAmount - $totalBranchWiseCurrentDayReturnSales - $totalBranchWiseCurrentDayExpenses + $totalBranchWiseCurrentDayIncomes),2);

        }
        return 0;
    }
}
