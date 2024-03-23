<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExistingUnitCostToPurchaseProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->float('existingUnitCost')->nullable()->after('discountedUnitCost');
            $table->float('existingDiscount')->nullable()->after('existingUnitCost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->float('existingUnitCost')->nullable()->after('discountedUnitCost');
            $table->float('existingDiscount')->nullable()->after('existingUnitCost');
        });
    }
}
