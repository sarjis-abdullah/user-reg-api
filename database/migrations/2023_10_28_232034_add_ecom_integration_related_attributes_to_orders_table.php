<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEcomIntegrationRelatedAttributesToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('ecomInvoice')->nullable()->after('comment');
            $table->string('orderUrl')->nullable()->after('ecomInvoice');
            $table->json('shipping')->nullable()->after('orderUrl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['ecomInvoice', 'orderUrl', 'shipping']);
        });
    }
}
