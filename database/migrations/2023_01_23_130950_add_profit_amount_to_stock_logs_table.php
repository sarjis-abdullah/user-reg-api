<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfitAmountToStockLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->double('profitAmount')->default(0)->after('quantity');
            $table->double('discountAmount')->default(0)->after('profitAmount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('stock_logs', 'unitProfit')) {
            Schema::table('stock_logs', function (Blueprint $table) {
                $table->dropColumn('profitAmount');
                $table->dropColumn('discountAmount');
            });
        }
    }
}
