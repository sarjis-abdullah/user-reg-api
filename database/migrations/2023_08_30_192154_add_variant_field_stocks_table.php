<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariantFieldStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('variations');
            $table->string('size', 20)->nullable()->after('expiredDate');
            $table->string('color', 20)->nullable()->after('size');
            $table->string('material', 20)->nullable()->after('color');
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
           $table->json('variations')->nullable()->after('expiredDate');
           $table->dropColumn('size');
           $table->dropColumn('color');
           $table->dropColumn('material');
        });
    }
}
