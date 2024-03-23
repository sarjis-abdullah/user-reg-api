<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_product_returns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned();
            $table->bigInteger('branchId')->unsigned()->nullable();
            $table->bigInteger('purchaseProductId')->unsigned();
            $table->integer('quantity');
            $table->date('date')->nullable();
            $table->string('comment')->nullable();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_product_returns');
    }
}
