<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfitAmountAttributeToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->double('profitAmount')->default(0)->after('amount');
            $table->double('grossProfit')->default(0)->after('profitAmount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('order_products', 'unitProfit')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->dropColumn('profitAmount');
                $table->dropColumn('grossProfit');
            });
        }
    }
}
