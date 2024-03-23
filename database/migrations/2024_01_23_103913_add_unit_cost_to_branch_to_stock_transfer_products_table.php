<?php

use App\Models\Stock;
use App\Models\StockTransferProduct;
use App\Repositories\Contracts\StockRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUnitCostToBranchToStockTransferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfer_products', function (Blueprint $table) {
            $table->double('unitCostToBranch',2)->nullable()->after('increaseCostPriceAmount');
        });

        StockTransferProduct::query()->chunk(50, function ($stps) {
            collect($stps)->each(function ($stp) {
                if($stp->increaseCostPriceAmount) {
                    $stockData = [
                        'productId' => $stp->productId,
                        'branchId' => $stp->fromBranchId
                    ];
                    if (isset($stp->sku)) {
                        $stockData['sku'] = $stp->sku;
                    }

                    $fromBranchStock = app(StockRepository::class)->findOneBy($stockData, true);

                    if($fromBranchStock instanceof  Stock) {
                        $unitCostToBranch = (float) $fromBranchStock->unitCost + (((float) $stp->increaseCostPriceAmount * (float) $fromBranchStock->unitCost)) / 100;

                        $stp->update(['unitCostToBranch' => $unitCostToBranch]);
                    }
                }
            });
        });

//      "sql": "UPDATE stock_transfer_products stp
//      JOIN stocks s ON stp.productId = s.productId AND stp.fromBranchId = s.branchId AND (stp.sku IS NULL OR s.sku = stp.sku)
//      SET stp.unitCostToBranch = s.unitCost + (stp.increaseCostPriceAmount * s.unitCost) / 100 WHERE stp.increaseCostPriceAmount > 0"
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfer_products', function (Blueprint $table) {
            $table->dropColumn('unitCostToBranch');
        });
    }
}
