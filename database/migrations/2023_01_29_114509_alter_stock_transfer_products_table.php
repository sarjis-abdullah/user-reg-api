<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStockTransferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column stockTransferId in stock_transfer_products table
        Schema::table('stock_transfer_products', function (Blueprint $table) {
            $table->unsignedBigInteger('stockTransferId')->after('productId')->nullable();

            $table->foreign('stockTransferId')->references('id')->on('stock_transfers');
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
            $table->dropForeign(['stockTransferId']);
            $table->dropColumn('stockTransferId');
        });
    }
}
