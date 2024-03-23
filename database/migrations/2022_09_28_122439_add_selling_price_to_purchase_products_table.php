<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellingPriceToPurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->double('sellingPrice')->nullable()->default(0)->after('unitCost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('purchase_products', 'sellingPrice'))
        {
            Schema::table('purchase_products', function (Blueprint $table)
            {
                $table->dropColumn('sellingPrice');
            });
        }
    }
}
