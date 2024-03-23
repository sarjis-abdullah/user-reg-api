<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncreaseCostPriceAmountToStockTransfersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfer_products', function (Blueprint $table) {
            $table->float('increaseCostPriceAmount')->nullable()->after('productId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfer_products', function (Blueprint $table) {
            $table->dropColumn('increaseCostPriceAmount');
        });
    }
}
