<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuantityAttributeTypeFromIntegerToFloat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->float('quantity')->change();
            $table->float('alertQuantity')->change();
        });

        Schema::table('stock_logs', function (Blueprint $table) {
            $table->float('prevQuantity')->change();
            $table->float('newQuantity')->change();
            $table->float('quantity')->change();
        });

        Schema::table('purchase_product_returns', function (Blueprint $table) {
            $table->float('quantity')->change();
        });

        Schema::table('order_product_returns', function (Blueprint $table) {
            $table->float('quantity')->change();
        });

        Schema::table('adjustments', function (Blueprint $table) {
            $table->float('quantity')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->float('alertQuantity')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('alertQuantity')->change();
        });

        Schema::table('stock_logs', function (Blueprint $table) {
            $table->integer('prevQuantity')->change();
            $table->integer('newQuantity')->change();
            $table->integer('quantity')->change();
        });

        Schema::table('purchase_product_returns', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('order_product_returns', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('adjustments', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('alertQuantity')->change();
        });
    }
}
