<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('companyId')->nullable();
            $table->unsignedBigInteger('branchId');
            $table->unsignedBigInteger('customerId')->nullable();
            $table->unsignedBigInteger('referenceId')->nullable();
            $table->date('date')->nullable();
            $table->string('terminal')->nullable();
            $table->string('invoice');
            $table->double('tax')->default(0);
            $table->double('shippingCost')->default(0);
            $table->double('discount')->default(0);
            $table->double('amount')->default(0);
            $table->double('paid')->default(0);
            $table->double('due')->default(0);
            $table->string('deliveryMethod')->nullable();
            $table->string('status')->nullable();
            $table->string('comment')->nullable();
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

            $table->foreign('companyId')
                ->references('id')->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('branchId')
                ->references('id')->on('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('customerId')
                ->references('id')->on('customers')
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
        Schema::dropIfExists('orders');
    }
}
