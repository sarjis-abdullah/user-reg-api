<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountedUnitPriceToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->double('discountedUnitPrice')->nullable()->after('unitPrice');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('order_products', 'discountedUnitPrice')) {
            Schema::table('order_products', function (Blueprint $table)
            {
                $table->dropColumn('discountedUnitPrice');
            });

        }
    }
}
