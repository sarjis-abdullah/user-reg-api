<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrevCostAndUnitPriceStockLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->double('prevUnitCost')->nullable()->after('quantity');
            $table->double('newUnitCost')->nullable()->after('prevUnitCost');
            $table->double('prevUnitPrice')->nullable()->after('newUnitCost');
            $table->double('newUnitPrice')->nullable()->after('prevUnitPrice');
            $table->dateTime('prevExpiredDate')->nullable()->after('newUnitPrice');
            $table->dateTime('newExpiredDate')->nullable()->after('prevExpiredDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropColumn([
                'prevUnitCost',
                'newUnitCost',
                'prevUnitPrice',
                'newUnitPrice',
                'prevExpiredDate',
                'newExpiredDate',
            ]);
        });
    }
}
