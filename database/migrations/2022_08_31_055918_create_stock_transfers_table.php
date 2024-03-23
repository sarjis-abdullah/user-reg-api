<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
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
            $table->unsignedBigInteger('fromBranchId');
            $table->unsignedBigInteger('toBranchId');
            $table->unsignedBigInteger('productId');
            $table->float('quantity');
            $table->double('totalAmount')->nullable();
            $table->string('comment')->nullable();
            $table->dateTime('date');
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('updatedByUserId')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
