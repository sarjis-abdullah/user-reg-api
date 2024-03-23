<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQuantityAfterDecimalValueFrom2To3InStpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfer_products', function (Blueprint $table) {
            $table->decimal('increaseCostPriceAmount', 8, 3)->change();
            $table->decimal('quantity', 8, 3)->change();
            $table->decimal('totalAmount', 8, 3)->change();
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
            $table->decimal('increaseCostPriceAmount', 8, 2)->change();
            $table->decimal('quantity', 8, 2)->change();
            $table->decimal('totalAmount', 8, 2)->change();
        });
    }
}
