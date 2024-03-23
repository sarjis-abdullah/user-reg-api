<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPreviousFinalDiscountAmountCalculationToPurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('purchase_products')
             ->whereNull('finalDiscountAmount')
             ->update(['finalDiscountAmount' => DB::raw('(CASE WHEN discountType = "percentage" THEN quantity * unitCost * discountAmount / 100 ELSE discountAmount END)')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('purchase_products')
            ->where('finalDiscountAmount', DB::raw('(CASE WHEN discountType = "percentage" THEN quantity * unitCost * discountAmount / 100 ELSE discountAmount END)'))
            ->update(['finalDiscountAmount' => null]);
    }
}
