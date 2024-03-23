<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('referenceNumber')->index();
            $table->unsignedBigInteger('fromBranchId');
            $table->unsignedBigInteger('toBranchId');
            $table->unsignedBigInteger('deliveryId')->nullable();
            $table->string('sendingNote')->nullable();
            $table->string('receivedNote')->nullable();
            $table->string('status', )->default('PENDING');
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('updatedByUserId')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fromBranchId')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('toBranchId')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('deliveryId')->references('id')->on('deliveries')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('createdByUserId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updatedByUserId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transfers');
    }
}
