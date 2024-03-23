<?php

namespace App\Services\Helpers;

use App\Models\Payment;
use App\Repositories\Contracts\BranchRepository;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfHelper
{
    /**
     * @param $data
     * @param $viewPath
     * @param $documentFileName
     * @return StreamedResponse
     * @throws MpdfException
     */
    public static function downloadPdf($data, $viewPath, $documentFileName = 'PDF-data-list'): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 42,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_bottom' => 12,
            'tempDir'=> base_path('storage/app/mpdf'),
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$documentFileName.'"'
        ];

        $branchRepo = app(BranchRepository::class);
        $allBranch = $branchRepo->findBy();
        if (request()->get('branchId')){
            $branch = $branchRepo->findOne(request()->get('branchId'));
            $branchName = $branch ? $branch->name : '';
        }else{
            $branchName = 'All Branch';
        }
        $html = view($viewPath, ['data' => $data, 'branch' => $branchName, 'all_branch' => $allBranch]);
        $html = $html->render();
        $mpdf->SetTitle($documentFileName);
        $mpdf->WriteHTML($html, 0);

        // Save PDF on your public storage
        Storage::disk('public')->put($documentFileName, $mpdf->Output($documentFileName, 'S'));

        // Get file back from storage with the give header information
        return Storage::disk('public')->download($documentFileName, 'Request', $header);
    }

    public static function paymentSource($paymentAbleType): string
    {
        return match ($paymentAbleType) {
            Payment::PAYMENT_SOURCE_ORDER                   => 'Sale',
            Payment::PAYMENT_SOURCE_ORDER_DUE               => 'Sale Due',
            Payment::PAYMENT_SOURCE_PURCHASE                => 'Purchase',
            Payment::PAYMENT_SOURCE_PURCHASE_DUE            => 'Purchase Due',
            Payment::PAYMENT_SOURCE_ORDER_PRODUCT_RETURN    => 'Sale Return',
            Payment::PAYMENT_SOURCE_PURCHASE_PRODUCT_RETURN => 'Purchase Return',
            Payment::PAYMENT_SOURCE_INCOME                  => 'Income',
            Payment::PAYMENT_SOURCE_EXPENSE                 => 'Expense',
        };
    }
}
