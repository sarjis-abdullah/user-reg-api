<?php

namespace App\Jobs;

use App\Repositories\Contracts\ProductRepository;
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

class CreateStockReportExportFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $products = $this->getProducts();

        if($this->export->status !== 'processing') {
            $this->export->update([
                'status' => 'processing',
                'fileName' => Carbon::now()->format('Y_m_d_H_i_s') . '_stock_report',
                'statusMessage' => "Job {$this->searchCriteria['page']} in export processing started"
            ]);

            //csv file headers
            $headers = [
                'id',
                'productName',
                'sku',
                'expiredDate',
                'branchName',
                'currentStock',
                'costPrice',
                'stockCostValue',
                'salePrice',
                'stockSaleValue'
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

        $mappedData = $products->map(function ($product){
            $data = [];
            if(count($product->stocks)) {
                $stocks = $product->stocks
                    ->where('quantity', '>', 0)
                    ->when(isset($this->searchCriteria['branchId']), function ($query) {
                        return $query->where('branchId', $this->searchCriteria['branchId']);
                    });

                $stocks->each(function ($stock) use ($product, &$data){
                    $data[] = [
                        'id' => $product->id,
                        'productName' => $product->name,
                        'sku' => $stock->sku ?? 'N/A',
                        'expiredDate' => $stock->expiredDate ?? 'N/A',
                        'branchName' => $stock->branch ? $stock->branch->name : 'N/A',
                        'currentStock' => $stock->quantity ?? 'N/A',
                        'costPrice' => $stock->unitCost ?? 'N/A',
                        'stockCostValue' => round(($stock->quantity * $stock->unitCost),2) ?? 'N/A',
                        'salePrice' => $stock->unitPrice ?? 'N/A',
                        'stockSaleValue' => round(($stock->quantity * $stock->unitPrice),2) ?? 'N/A',
                    ];
                });
            }

            return $data;
        })->toArray();

        $csv->insertAll(call_user_func_array('array_merge', $mappedData));

        $this->searchCriteria['page'] = $products->hasMorePages() ? $products->currentPage() + 1 : null;

        if (is_null($this->searchCriteria['page']) || (!is_null($this->searchCriteria['last_page']) && (int) $this->searchCriteria['page'] > (int) $this->searchCriteria['last_page'])) {
            if($this->export->exportAs == 'pdf') {

                self::setBranchName();

                $csvFilePath = $this->fileSystemAdapter->path($this->directoryName . '/' .$this->export->fileName . '.csv');
                $csv = Reader::createFromPath($csvFilePath);

                $csv->setHeaderOffset(0); // Assuming the first row is the header

                $dataChunks = array_chunk(collect(iterator_to_array($csv->getRecords()))->groupBy('id')->toArray(), 300);

                $pdf = self::initializeMpdf();

                foreach ($dataChunks as $key => $chunkData) {
                    $pdf->SetTitle($this->export->fileName);

                    if($key != 0) {
                        $pdf->AddPage();
                    }

                    $pdf->WriteHTML(view($this->export->viewPath, [
                        'serial' => $key * 300 == 0 ? 1 : $key * 300,
                        'products' => $chunkData,
                        'branch' => $this->branchName
                    ]));
                }

                self::putProcessedPdf($pdf);
            }

            $this->header = 'Attachment of Generated Stock Report';
            $this->subject = 'Stock Report Generated';

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
    public function getProducts() {
        return app(ProductRepository::class)->stocks(array_diff_key($this->searchCriteria, array_flip(['last_page', 'items'])))['products'];
//        return app(ProductRepository::class)->findBy(array_diff_key($this->searchCriteria, array_flip(['last_page', 'items'])))['products'];
    }
}
