<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseAndStockTableForVariation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn([
                'size',
                'color',
                'material'
            ]);

            $table->unsignedBigInteger('productVariationId')->nullable()->after('productId');
        });

        Schema::table('purchase_products', function (Blueprint $table) {
            $table->unsignedBigInteger('productVariationId')->nullable()->after('productId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('size', 20)->nullable()->after('expiredDate');
            $table->string('color', 20)->nullable()->after('size');
            $table->string('material', 20)->nullable()->after('color');

            $table->dropColumn('productVariationId');
        });

        Schema::table('purchase_products', function (Blueprint $table) {
            $table->dropColumn('productVariationId');
        });
    }
}
