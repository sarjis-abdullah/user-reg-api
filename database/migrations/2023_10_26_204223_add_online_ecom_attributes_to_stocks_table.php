<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnlineEcomAttributesToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->integer('wcStockId')->nullable()->after('createdByUserId');
            $table->dateTime('ecomPublishedAt')->nullable()->after('wcStockId');
            $table->string('permalink')->nullable()->after('ecomPublishedAt');
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
            $table->dropColumn('wcStockId');
            $table->dropColumn('ecomPublishedAt');
            $table->dropColumn('permalink');
        });
    }
}
