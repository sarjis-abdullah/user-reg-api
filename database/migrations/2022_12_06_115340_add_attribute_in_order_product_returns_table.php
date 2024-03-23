<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributeInOrderProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('orderId')->nullable()->after('branchId');

            $table->foreign('orderId')
                ->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('order_product_returns', 'purchaseId'))
        {
            Schema::table('order_product_returns', function (Blueprint $table)
            {
                $table->dropForeign(['orderId']);
                $table->dropColumn('orderId');
            });

        }
    }
}
