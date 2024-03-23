<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId');
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('supplierId');
            $table->string('reference')->nullable();
            $table->date('date')->nullable();
            $table->double('totalAmount');
            $table->double('discountAmount')->nullable();
            $table->double('shippingCost')->nullable();
            $table->double('taxAmount')->nullable();
            $table->double('paid')->nullable()->default(0);
            $table->double('due')->nullable()->default(0);
            $table->text('note')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
