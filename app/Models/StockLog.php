<?php

namespace App\Models;

use App\Models\Traits\CommonModelFeatures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockLog extends Model
{
    use CommonModelFeatures;

    const TYPE_PURCHASE_PRODUCT = 'purchase-product';
    const TYPE_PURCHASE_PRODUCT_RETURN = 'purchase-product-return';
    const TYPE_ORDER_PRODUCT = 'order-product';
    const TYPE_ORDER_PRODUCT_RETURN = 'order-product-return';
    const TYPE_ORDER_PRODUCT_CANCELLED = 'order-product-cancelled';
    const TYPE_ADJUSTMENT_PRODUCT_INCREMENT = 'adjustment-product-increment';
    const TYPE_ADJUSTMENT_PRODUCT_DECREMENT = 'adjustment-product-decrement';
    const TYPE_STOCK_TRANSFER_TO_BRANCH = 'transfer-to-branch';
    const TYPE_STOCK_TRANSFER_FROM_BRANCH = 'transfer-from-branch';
    const TYPE_STOCK_TRANSFER_REVERT_FROM_BRANCH = 'transfer-revert-from-branch';
    const TYPE_OPENING_STOCK_TO_BRANCH = 'opening-stock-to-branch';

    const TYPE_UNIT_COST_CHANGED = 'unit-cost-changed';
    const TYPE_UNIT_PRICE_CHANGED = 'unit-price-changed';

    const TYPE_STOCK_MERGE_UPDATE = 'stock-merge-update';
    const TYPE_STOCK_MERGE_DELETE = 'stock-merge-delete';
    const TYPE_STOCK_MOVED_TO_BUNDLE_PRODUCT = 'stock-moved-to-bundle-product';
    const TYPE_RESTOCK_FROM_BUNDLE_PRODUCT = 'restock-from-bundle-product';
    const TYPE_PLAT_FORM_SALE = 'platform-sale';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'createdByUserId',
        'stockId',
        'referenceNumber',
        'type',
        'resourceId',
        'productId',
        'prevQuantity',
        'newQuantity',
        'quantity',
        'prevUnitCost',
        'newUnitCost',
        'prevUnitPrice',
        'newUnitPrice',
        'prevExpiredDate',
        'newExpiredDate',
        'profitAmount',
        'discountAmount',
        'date',
        'receivedBy',
        'note',
        'updatedByUserId',
    ];

    /**
     * get the stock
     *
     * @return BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stockId');
    }

    /**
     * get the product
     *
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'productId');
    }

    /**
     * get the details based on type
     *
     * @return HasOne
     */
    public function detailByType(): HasOne
    {
        return $this->hasOne($this->getResourceClassByType(), 'id', 'resourceId');
    }

    /**
     * stock log has different types,
     * get the relationship class by types
     *
     * @return string
     */
    private function getResourceClassByType(): string
    {
        $resourceClass = '';

        switch ($this->type) {
            case self::TYPE_ADJUSTMENT_PRODUCT_DECREMENT:
            case self::TYPE_ADJUSTMENT_PRODUCT_INCREMENT:
                $resourceClass = Adjustment::class;
                break;
            case self::TYPE_STOCK_TRANSFER_TO_BRANCH:
            case self::TYPE_STOCK_TRANSFER_FROM_BRANCH:
            case self::TYPE_STOCK_TRANSFER_REVERT_FROM_BRANCH:
                $resourceClass = StockTransferProduct::class;
                break;
            case self::TYPE_PURCHASE_PRODUCT:
                $resourceClass = PurchaseProduct::class;
                break;
            case self::TYPE_PURCHASE_PRODUCT_RETURN:
                $resourceClass = PurchaseProductReturn::class;
                break;
            case self::TYPE_ORDER_PRODUCT:
                $resourceClass = OrderProduct::class;
                break;
            case self::TYPE_ORDER_PRODUCT_RETURN:
                $resourceClass = OrderProductReturn::class;
                break;
            case self::TYPE_ORDER_PRODUCT_CANCELLED:
                $resourceClass = OrderProduct::class;
                break;
            case self::TYPE_UNIT_PRICE_CHANGED:
            case self::TYPE_UNIT_COST_CHANGED:
            case self::TYPE_STOCK_MOVED_TO_BUNDLE_PRODUCT:
                $resourceClass = Stock::class;
                break;
        }

        return $resourceClass;
    }
}
