<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountIdAndTaxIdToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('taxId')->nullable()->after('tax');
            $table->unsignedBigInteger('discountId')->nullable()->after('discount');

            $table->foreign('discountId')
                ->references('id')->on('discounts');

            $table->foreign('taxId')
                ->references('id')->on('taxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('order_products', 'taxId'))
        {
            Schema::table('order_products', function (Blueprint $table)
            {
                $table->dropForeign(['taxId']);
                $table->dropColumn('taxId');
            });
            Schema::table('order_products', function (Blueprint $table)
            {
                $table->dropForeign(['discountId']);
                $table->dropColumn('discountId');
            });
        }
    }
}
