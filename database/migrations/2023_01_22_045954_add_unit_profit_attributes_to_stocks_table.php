<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitProfitAttributesToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->double('unitProfit')->nullable()->after('unitPrice');
            $table->double('stockProfit')->default(0)->after('unitProfit');
            $table->double('discountAmount')->default(0)->after('stockProfit');
            $table->double('grossProfit')->default(0)->after('discountAmount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('stocks', 'unitProfit')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('unitProfit');
                $table->dropColumn('stockProfit');
                $table->dropColumn('discountAmount');
                $table->dropColumn('grossProfit');
            });

        }
    }
}
