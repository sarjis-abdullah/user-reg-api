<?php


namespace App\Repositories;


use App\Events\PurchaseProduct\PurchaseProductCreatedEvent;
use App\Models\Attachment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PurchaseProductReturnRepository;
use App\Repositories\Contracts\PurchaseRepository;
use App\Repositories\Contracts\PurchaseProductRepository;
use App\Services\Helpers\BarcodeGenerateHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EloquentPurchaseRepository extends EloquentBaseRepository implements PurchaseRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;


        if (isset($searchCriteria['query'])) {
            $searchCriteria['id'] = $this->model->where('reference', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhere('status', 'like', '%'    . $searchCriteria['query'] . '%')
                ->orWhere('paymentStatus', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('supplier', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%')
                          ->orWhere('phone', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        if (isset($searchCriteria['paymentMethod'])){
            $searchCriteria['id'] = $this->model
                ->orWhereHas('payments', function($query) use ($searchCriteria){
                    $query->where('method', '=', $searchCriteria['paymentMethod']);
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['paymentMethod']);
        }

        if (isset($searchCriteria['endDate'])) {
            $queryBuilder  =  $queryBuilder->whereDate('created_at', '<=', Carbon::parse($searchCriteria['endDate']));
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder =  $queryBuilder->whereDate('created_at', '>=', Carbon::parse($searchCriteria['startDate']));
            unset($searchCriteria['startDate']);
        }
        if (isset($searchCriteria['id'])) {
            $searchCriteria['id'] = is_array($searchCriteria['id']) ? implode(",", array_unique($searchCriteria['id'])) : $searchCriteria['id'];
        }
        if (isset($searchCriteria['paymentStatusGroup'])){
            $allPaymentStatus = explode(',', $searchCriteria['paymentStatusGroup']);
            $queryBuilder = $queryBuilder->whereIn('paymentStatus', $allPaymentStatus);
            unset($searchCriteria['paymentStatusGroup']);
        }
        if (isset($searchCriteria['purchaseStatus'])){
            $queryBuilder =  $queryBuilder->where('status', $searchCriteria['purchaseStatus']);
            unset($searchCriteria['purchaseStatus']);
        }
        if (isset($searchCriteria['purchaseStatusGroup'])){
            $allPurchaseStatus = explode(',', $searchCriteria['purchaseStatusGroup']);
            $queryBuilder = $queryBuilder->whereIn('status', $allPurchaseStatus);
            unset($searchCriteria['purchaseStatusGroup']);
        }

        $withSummary = false;
        if (isset($searchCriteria['withSummary'])) {
            unset($searchCriteria['withSummary']);
            $withSummary = true;
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $queryBuilder->withSum('purchaseProducts', 'finalDiscountAmount');

        $summary = [];
        if ($withSummary) {
            $allData = $queryBuilder->get();
            $summary['totalPurchaseAmount'] = round($allData->sum('totalAmount'), 2);
            $summary['totalNetPurchaseAmount'] = round(($allData->sum('totalAmount') - $allData->sum('returnedAmount')), 2);
            $summary['totalPaid'] = round($allData->sum('paid'), 2);
            $summary['totalNetPaid'] = round(($allData->sum('paid') - $allData->sum('gettableDueAmount')), 2);
            $summary['totalDue'] = round($allData->sum('due'), 2);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1;
            $purchases = $queryBuilder->paginate($limit, ['*'], 'page', $page);
        } else {
            $purchases = $queryBuilder->get();
        }
        $pageWiseSummary = [];

        $pageWiseSummary['totalPurchaseAmount'] = round($purchases->sum('totalAmount'), 2);
        $pageWiseSummary['totalNetPurchaseAmount'] = round(($purchases->sum('totalAmount') - $purchases->sum('returnedAmount')), 2);
        $pageWiseSummary['totalPaid'] = round($purchases->sum('paid'), 2);
        $pageWiseSummary['totalNetPaid'] = round(($purchases->sum('paid') - $purchases->sum('gettableDueAmount')), 2);
        $pageWiseSummary['totalDue'] = round($purchases->sum('due'), 2);

        if ($withSummary){
            return ['purchases' => $purchases, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        }

        return ['purchases' => $purchases, 'pageWiseSummary' => $pageWiseSummary];
    }

    /**
     * @inheritdoc
     */
    public function findByPurchaseReturnProducts(array $searchCriteria = [], $withTrashed = false)
    {
        $queryBuilder = $this->model;

        if (isset($searchCriteria['query'])) {

            $ppr = app(PurchaseProductRepository::class);
            $productIds = $ppr->model->orWhereHas('product', function($query) use ($searchCriteria){
                                            $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                                        })->pluck('productId')->toArray();

            $searchCriteria['id'] = $this->model->where('reference', 'like', '%' . $searchCriteria['query'] . '%')
                ->orWhereHas('supplier', function($query) use ($searchCriteria){
                    $query->where('name', 'like', '%' . $searchCriteria['query'] . '%');
                })
                ->orWhereHas('purchaseProducts', function($query) use ($searchCriteria, $productIds){
                    $query->whereIn('productId', $productIds);
                })
                ->pluck('id')->toArray();
            unset($searchCriteria['query']);
        }
        if (isset($searchCriteria['endDate'])) {
            $queryBuilder = $queryBuilder->whereHas('purchaseProductReturns', function($query) use ($searchCriteria){
                $query->where('purchase_product_returns.created_at', '<=', Carbon::parse($searchCriteria['endDate'])->endOfDay());
            });
            unset($searchCriteria['endDate']);
        }

        if (isset($searchCriteria['startDate'])) {
            $queryBuilder = $queryBuilder->whereHas('purchaseProductReturns', function ($query) use ($searchCriteria) {
                $query->where('purchase_product_returns.created_at', '>=', Carbon::parse($searchCriteria['startDate'])->startOfDay());
            });
            unset($searchCriteria['startDate']);
        }

        $withSummary = false;
        if (!empty($searchCriteria['withSummary'])) {
            unset($searchCriteria['withSummary']);
            $withSummary = true;
        }

        $queryBuilder = $queryBuilder->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15;
        $orderBy = !empty($searchCriteria['order_by']) ? $searchCriteria['order_by'] : 'id';
        $orderDirection = !empty($searchCriteria['order_direction']) ? $searchCriteria['order_direction'] : 'desc';
        $queryBuilder->orderBy($orderBy, $orderDirection);

        $queryBuilder->withCount('purchaseProductReturns');
        $queryBuilder->withSum('purchaseProductReturns', 'returnAmount');
        $queryBuilder->withSum('purchaseProductReturns', 'quantity');
        $queryBuilder->having('purchase_product_returns_count', '>', 0);

        if ($withTrashed) {
            $queryBuilder->withTrashed();
        }

        $summary = [];

        if ($withSummary) {
            $allReturnPurchaseData = $queryBuilder->get();
            $summary['totalReturnAmount'] = round($allReturnPurchaseData->sum('purchase_product_returns_sum_return_amount'),2);
            $summary['totalReturnQuantity'] = round($allReturnPurchaseData->sum('purchase_product_returns_sum_quantity'),2);
        }

        if (empty($searchCriteria['withoutPagination'])) {
            $returnPurchases = $queryBuilder->paginate($limit);
        } else {
            $returnPurchases = $queryBuilder->get();
        }

        $pageWiseSummary = [];

        $pageWiseSummary['totalReturnAmount'] = round($returnPurchases->sum('purchase_product_returns_sum_return_amount'),2);
        $pageWiseSummary['totalReturnQuantity'] = round($returnPurchases->sum('purchase_product_returns_sum_quantity'),2);

        if ($withSummary){
            return ['returnPurchases' => $returnPurchases, 'summary' => $summary, 'pageWiseSummary' => $pageWiseSummary];
        }

        return ['returnPurchases' => $returnPurchases, 'pageWiseSummary' => $pageWiseSummary];

    }

    /**
     * @inheritdoc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $data['status'] = $data['status'] ?? Purchase::STATUS_RECEIVED;

        $purchase = parent::save($data);

        if (!empty($purchase->reference)) {
            $barcodeData['resourceId'] = $purchase->id;
            $barcodeData['barcode'] = $purchase->reference;
            $barcodeData['barcodeType'] = Product::BARCODE_TYPE_CODE_128;
            $barcodeData['attachmentType'] = Attachment::ATTACHMENT_TYPE_REFERENCE;

            BarcodeGenerateHelper::generateBarcode($barcodeData);
        }

        $purchaseProductRepository = app(PurchaseProductRepository::class);

        foreach ($data['purchaseProducts'] as $product) {
            $purchaseProductData = $product;
            $purchaseProductData['purchaseId'] = $purchase->id;
            $purchaseProductData['branchId'] = $purchase->branchId;
            $purchaseProductData['createdByUserId'] = $purchase->createdByUserId;
            $purchaseProductData['date'] = $purchase->date;
            $purchaseProductData['status'] = $purchase->status;
            $purchaseProductData['finalDiscountAmount'] = $this->getFinalDiscountAmount($purchaseProductData);

            $totalTaxAmount = 0;
            $totalDiscountAmount = 0;
            if (isset($product['existingUnitCost']) && isset($product['existingDiscount']) && isset($product['tax']) && isset($product['discountAmount']) && isset($product['discountType'])) {
                $exactUnitCost = ($product['existingUnitCost'] / $product['quantity']);

                $exactTaxAmount = (($exactUnitCost * $product['tax']) / 100);
                $totalTaxAmount = ($product['quantity'] * $exactTaxAmount);

                if ($product['discountType'] == 'flat'){
                    $totalDiscountAmount = $product['discountAmount'];
                }else{
                    $exactDiscountAmount = (($exactUnitCost * $product['existingDiscount']) / 100);
                    $totalDiscountAmount = ($product['quantity'] * $exactDiscountAmount);
                }
            }
            $purchaseProductData['totalTaxAmount'] = $totalTaxAmount;
            $purchaseProductData['totalDiscountAmount'] = $totalDiscountAmount;
            $purchaseProductData['purchaseQuantity'] = isset($product['purchaseQuantity']) ? $product['purchaseQuantity'] : 0;

            $purchaseProductRepository->save($purchaseProductData);
        }

        if(isset($data['payment'])) {
            $paymentData = $data['payment'];
            $paymentData['status'] = Payment::STATUS_SUCCESS;
            $paymentData['cashFlow'] = Payment::CASH_FLOW_OUT;
            $paymentData['paymentableId'] = $purchase->id;
            $paymentData['paymentableType'] = Payment::PAYMENT_SOURCE_PURCHASE;
            $paymentData['payType'] = Payment::PAY_TYPE_PURCHASE;
            $paymentData['receivedByUserId'] = $purchase->createdByUserId;
            $paymentData['date'] = Carbon::now();

            $paymentRepository = app(PaymentRepository::class);
            $paymentRepository->save($paymentData);
        } else {
            parent::update($purchase, ['paymentStatus' => Payment::PAYMENT_STATUS_UNPAID]);
        }

        DB::commit();

        return $purchase;
    }

    /**
     * @inheritdoc
     */
    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $purchase = parent::update($model, $data);

        if(isset($data['purchaseProducts'])) {
            $purchaseProductRepository = app(PurchaseProductRepository::class);

            $purchaseProductRepository->model->where('purchaseId', $model->id)->delete();

            foreach ($data['purchaseProducts'] as $product) {
                $purchaseProductData = $product;
                $purchaseProductData['purchaseId'] = $purchase->id;
                $purchaseProductData['branchId'] = $purchase->branchId;
                $purchaseProductData['createdByUserId'] =  $this->getLoggedInUser()->id;
                $purchaseProductData['date'] = $purchase->date;

                $purchaseProduct = $purchaseProductRepository->save($purchaseProductData);
            }
        }

        DB::commit();

        return $purchase;
    }

    /**
     * @param \ArrayAccess $model
     * @param array $data
     * @return \ArrayAccess
     */
    public function statusUpdate(\ArrayAccess $model, array $data): \ArrayAccess
    {
        if($model->status == $data['status']) {
            throw ValidationException::withMessages([
                'status' => ['You have to select different stauts to update']
            ]);
        }

        if($model->status == Purchase::STATUS_RECEIVED) {
            throw ValidationException::withMessages([
                'status' => ['You can`t change received status of a purchase.']
            ]);
        }

        DB::beginTransaction();

        $data['date'] = today();
        $purchase = parent::update($model, $data);

        if($data['status'] == Purchase::STATUS_RECEIVED) {
            $purchaseProducts = $purchase->purchaseProducts;

            foreach ($purchaseProducts as $purchaseProduct) {
                $serialIds = $purchaseProduct->serialIds ?? [];
                event(new PurchaseProductCreatedEvent($purchaseProduct, $serialIds));
            }
        }

        DB::commit();

        return $purchase;
    }

    public function getFinalDiscountAmount($data)
    {
        $discountType = $data['discountType'];
        $discountAmount = $data['discountAmount'];
        $quantity = $data['quantity'];
        $unitCost = $data['unitCost'];
        $finalDiscountInAmount = 0;
        if ($discountType === 'percentage'){
            $finalDiscountInAmount = ( $quantity * $unitCost * $discountAmount) / 100;
        }else{
            $finalDiscountInAmount = $discountAmount;
        }
        return $finalDiscountInAmount;
    }
}
