<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveProductIdAddStockIdToAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->dropForeign(['productId']);
            $table->dropColumn(['productId']);
            $table->foreignId('stockId')
                ->nullable()
                ->after("branchId")
                ->references('id')->on('stocks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->foreignId('productId')
                ->after("branchId")
                ->references('id')->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->dropForeign(['stockId']);
            $table->dropColumn(['stockId']);
        });
    }
}
