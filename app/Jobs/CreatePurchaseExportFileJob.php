<?php

namespace App\Jobs;

use App\Repositories\Contracts\PurchaseRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Writer;
use Mpdf\MpdfException;
use PDF;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

class CreatePurchaseExportFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //this trait has all the same properties of export
    use CommonExportFileJobTrait;

    /**
     * Execute the job.
     *
     * @return void
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException|FileNotFoundException
     * @throws Exception
     */
    public function handle()
    {
        $items = $this->getPurchases();

        if($this->export->status !== 'processing') {
            $this->export->update([
                'status' => 'processing',
                'fileName' => Carbon::now()->format('Y_m_d_H_i_s') . '_purchase_list',
                'statusMessage' => "Job {$this->searchCriteria['page']} in export processing started"
            ]);

            //csv file headers
            $headers = [
                'id',
                'date',
                'reference',
                'supplierName',
                'branchName',
                'totalAmount',
                'discountAmount',
                'shippingCost',
                'taxAmount',
                'paid',
                'due',
                'returnedAmount',
                'paymentStatus',
                'status',
                'note'
            ];

            // Create a new CsvWriter instance and specify the output file path and
            $csvFilePath = $this->fileSystemAdapter->path($this->directoryName . '/' .$this->export->fileName . '.csv');
            $csv = Writer::createFromPath($csvFilePath, 'w+');
            $csv->insertOne($headers);

        } elseif($this->export->status === 'processing') {
            $this->export->update([
                'statusMessage' => "Job {$this->searchCriteria['page']} in export processing started"
            ]);
        }

        $csvFilePath = $this->fileSystemAdapter->path($this->directoryName . '/' .$this->export->fileName . '.csv');
        $csv = Writer::createFromPath($csvFilePath, 'a+');

        $mappedData = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'date' => $item->date,
                'reference' => $item->reference,
                'supplierName' => $item->supplier ? $item->supplier->name : 'N/A',
                'branchName' => $item->branch ? $item->branch->name : 'N/A',
                'totalAmount' => $item->totalAmount,
                'discountAmount' => $item->discountAmount,
                'shippingCost' => $item->shippingCost,
                'taxAmount' => $item->taxAmount,
                'paid' => $item->paid,
                'due' => $item->due,
                'returnedAmount' => $item->returnedAmount,
                'paymentStatus' => $item->paymentStatus,
                'status' => $item->status,
                'note' => $item->note,
            ];
        });

        $csv->insertAll($mappedData);

        $this->searchCriteria['page'] = $items->hasMorePages() ? $items->currentPage() + 1 : null;

        if (is_null($this->searchCriteria['page']) || (!is_null($this->searchCriteria['last_page']) && (int) $this->searchCriteria['page'] > (int) $this->searchCriteria['last_page'])) {
            if($this->export->exportAs == 'pdf') {
                self::setBranchName();

                $csvFilePath = $this->fileSystemAdapter->path($this->directoryName . '/' .$this->export->fileName . '.csv');
                $csv = Reader::createFromPath($csvFilePath);

                $csv->setHeaderOffset(0); // Assuming the first row is the header

                $dataChunks = array_chunk(iterator_to_array($csv->getRecords()), 500);

                $pdf = self::initializeMpdf();

                foreach ($dataChunks as $key => $chunkData) {
                    $pdf->SetTitle($this->export->fileName);

                    if($key != 0) {
                        $pdf->AddPage();
                    }

                    $pdf->WriteHTML(view($this->export->viewPath, [
                        'serial' => $key * 500 == 0 ? 1 : $key * 500,
                        'items' => $chunkData,
                        'branch' => $this->branchName
                    ]));
                }

                self::putProcessedPdf($pdf);
            }

            $this->header = 'Attachment of Generated Purchase Lists';
            $this->subject = 'Purchase List Generated';

            self::sendEmailToUser();

            return;
        }

        // Refresh to get the current state of export before using it for the next job
        $this->export->refresh();

        dispatch(new static($this->export, $this->searchCriteria));
    }

    /**
     * @return mixed
     */
    public function getPurchases() {
        return app(PurchaseRepository::class)->findBy(array_diff_key($this->searchCriteria, array_flip(['last_page', 'items'])))['purchases'];
    }
}
