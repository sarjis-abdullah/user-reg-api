<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountedUnitCostAndDiscountAmountToPurchaseProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->double('discountedUnitCost')->nullable()->after('unitCost');
            $table->string('discountType', 20)->nullable()->after('discountAmount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('purchase_products', 'discountedUnitCost')) {
            Schema::table('purchase_products', function (Blueprint $table)
            {
                $table->dropColumn('discountedUnitCost');
                $table->dropColumn('discountType');
            });

        }
    }
}
