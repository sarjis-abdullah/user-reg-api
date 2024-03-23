<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentSummaryExport implements FromView
{
    public $paymentSummary;
    public function __construct($paymentSummary)
    {
        $this->paymentSummary = $paymentSummary;
    }

    public function view(): View
    {
        return view('excel.report.paymentSummary', [
            'data' => $this->paymentSummary
        ]);
    }
}
