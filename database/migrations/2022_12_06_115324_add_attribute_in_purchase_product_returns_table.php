<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributeInPurchaseProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_product_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('purchaseId')->nullable()->after('branchId');

            $table->foreign('purchaseId')
                ->references('id')->on('purchases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('purchase_product_returns', 'purchaseId'))
        {
            Schema::table('purchase_product_returns', function (Blueprint $table)
            {
                $table->dropForeign(['purchaseId']);
                $table->dropColumn('purchaseId');
            });

        }
    }
}
