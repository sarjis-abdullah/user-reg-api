<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGettableDueAmountToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->double('gettableDueAmount')->nullable()->default(0)->after('due');
            $table->double('returnedAmount')->nullable()->default(0)->after('paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('purchases', 'gettableDueAmount')) {
            Schema::table('purchases', function (Blueprint $table)
            {
                $table->dropColumn('gettableDueAmount');
                $table->dropColumn('returnedAmount');
            });

        }
    }
}
