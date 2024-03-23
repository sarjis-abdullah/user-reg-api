<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned()->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->bigInteger('categoryId')->unsigned()->nullable();
            $table->float('amount');
            $table->string('paymentType')->nullable();
            $table->date('expenseDate');
            $table->string('expenseReason');
            $table->text('notes')->nullable();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('categoryId')
                ->references('id')->on('expense_categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('branchId')
                ->references('id')->on('branches')
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
        Schema::dropIfExists('expenses');
    }
}
