<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned();
            $table->unsignedBigInteger('orderId')->nullable();
            $table->unsignedBigInteger('purchaseId')->nullable();
            $table->string('cashFlow', 20)->nullable();
            $table->string('method', 20);
            $table->double('amount');
            $table->float('receivedAmount', 9, 3)->nullable();
            $table->float('changedAmount', 9, 3)->nullable();
            $table->string('txnNumber', 40)->nullable();
            $table->string('referenceNumber')->nullable();
            $table->dateTime('date');
            $table->string('status');
            $table->bigInteger('receivedByUserId')->unsigned()->nullable();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('orderId')
                ->references('id')->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('receivedByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('updatedByUserId')
                ->references('id')->on('users')
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
        Schema::dropIfExists('payments');
    }
}
