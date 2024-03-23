<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusInOrdersAndPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('paymentStatus')->nullable()->after('status');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('paymentStatus')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('purchases', 'paymentStatus'))
        {
            Schema::table('purchases', function (Blueprint $table)
            {
                $table->dropColumn('paymentStatus');
            });
        }
        if (Schema::hasColumn('orders', 'paymentStatus'))
        {
            Schema::table('orders', function (Blueprint $table)
            {
                $table->dropColumn('paymentStatus');
            });
        }
    }
}
