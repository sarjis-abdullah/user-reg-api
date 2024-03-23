<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributesInOrderProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product_returns', function (Blueprint $table) {
            $table->double('profitAmount')->default(0)->after('returnAmount');
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
        if (Schema::hasColumn('order_product_returns', 'unitProfit')) {
            Schema::table('order_product_returns', function (Blueprint $table) {
                $table->dropColumn('profitAmount');
                $table->dropColumn('discountAmount');
            });
        }
    }
}
