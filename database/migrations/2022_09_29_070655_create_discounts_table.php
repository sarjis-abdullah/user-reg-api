<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned();
            $table->string('title')->nullable();
            $table->string('type');
            $table->double('amount');
            $table->dateTime('startDate');
            $table->dateTime('endDate');
            $table->text('note')->nullable();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
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
        Schema::dropIfExists('discounts');
    }
}
