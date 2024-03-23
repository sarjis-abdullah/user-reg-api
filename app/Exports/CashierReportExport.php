<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashierReportExport implements FromView
{
    public $cashier;
    public function __construct($cashier)
    {
        $this->cashier = $cashier;
    }

    public function view(): View
    {
        return view('excel.report.cashierReport', [
            'data' => $this->cashier
        ]);
    }
}
