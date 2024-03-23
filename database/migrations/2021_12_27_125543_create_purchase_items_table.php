<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId');
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('purchaseId');
            $table->unsignedBigInteger('productId');
            $table->date('date')->nullable();
            $table->string('sku')->nullable();
            $table->float('quantity');
            $table->double('unitCost')->nullable();
            $table->double('discountAmount')->nullable();
            $table->double('taxAmount')->nullable();
            $table->double('totalAmount')->nullable();
            $table->date('expiredDate')->nullable();
            $table->unsignedBigInteger('managedByUserId')->nullable();
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
        Schema::dropIfExists('purchase_products');
    }
}
