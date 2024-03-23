<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned();
            $table->bigInteger('categoryId')->unsigned();
            $table->bigInteger('branchId')->unsigned()->nullable();
            $table->double('amount');
            $table->text('sourceOfIncome')->nullable();
            $table->dateTime('date');
            $table->string('paymentType')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('incomes');
    }
}
