<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('orderId');
            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('stockId');
            $table->date('date')->nullable();
            $table->double('unitPrice');
            $table->double('quantity')->nullable();
            $table->double('discount')->default(0)->nullable();
            $table->double('tax')->default(0)->nullable();
            $table->double('amount')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('updatedByUserId')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('updatedByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('orderId')
                ->references('id')->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('productId')
                ->references('id')->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('stockId')
                ->references('id')->on('stocks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
