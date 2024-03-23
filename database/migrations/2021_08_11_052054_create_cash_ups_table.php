<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('companyId')->nullable();
            $table->unsignedBigInteger('branchId');
            $table->dateTime('openedDate');
            $table->string('openedBy');
            $table->double('openedCash')->default(0);
            $table->double('cashIn')->default(0);
            $table->double('cashOut')->default(0);
            $table->double('closedCash')->default(0) ;
            $table->dateTime('closedDate')->nullable();
            $table->string('closedBy')->nullable();
            $table->string('openedNotes')->nullable();
            $table->string('closedNotes')->nullable();
            $table->double('dues')->default(0);
            $table->double('cards')->default(0);
            $table->double('cheques')->default(0);
            $table->double('mBanking')->default(0);
            $table->double('total')->default(0);
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

            $table->foreign('companyId')
                ->references('id')->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('branchId')
                ->references('id')->on('branches')
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
        Schema::dropIfExists('cash_ups');
    }
}
