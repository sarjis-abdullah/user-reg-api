<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnAmountToPurchaseProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_product_returns', function (Blueprint $table) {
            $table->double('returnAmount')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('purchase_product_returns', 'returnAmount'))
        {
            Schema::table('purchase_product_returns', function (Blueprint $table)
            {
                $table->dropColumn('returnAmount');
            });
        }
    }
}
