<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'stock_transfer', 'purchase_order', 'sale_order'
            $table->unsignedBigInteger('deliveryAgencyId')->nullable();
            $table->string('deliveryPersonName')->nullable();
            $table->string('trackingNumber')->nullable()->index();
            $table->string('fromDeliveryPhone')->nullable();
            $table->string('toDeliveryPhone')->nullable();
            $table->string('fromDeliveryAddress')->nullable();
            $table->string('toDeliveryAddress')->nullable();
            $table->string('status')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('updatedByUserId')->nullable();

            $table->foreign('createdByUserId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updatedByUserId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('deliveryAgencyId')->references('id')->on('delivery_agencies')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
