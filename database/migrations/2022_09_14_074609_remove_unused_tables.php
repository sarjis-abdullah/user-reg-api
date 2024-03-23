<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('invoice_settings');
        Schema::dropIfExists('pos_settings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned()->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->string('businessName');
            $table->date('startDate');
            $table->string('currency');
            $table->string('currencySymbolPlacement');
            $table->string('financialYearStartMonth');
            $table->string('stockAccountingMethod');
            $table->string('timeZone')->nullable();
            $table->string('timeFormat')->nullable();
            $table->string('dateFormat')->nullable();

            $table->timestamps();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
            $table->softDeletes();

            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('updatedByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('branchId')
                ->references('id')->on('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        //
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned()->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->string('invoiceAddress');
            $table->string('invoiceEmail');
            $table->string('mobileNumber');
            $table->string('invoicePrefix');
            $table->string('invoiceFooterContent');
            $table->timestamps();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
            $table->softDeletes();

            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('updatedByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('branchId')
                ->references('id')->on('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });

        //
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned()->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->string('defaultCustomer');
            $table->string('storeNo');
            $table->string('defaultBiller');
            $table->integer('displayeNumberOfProductRow');
            $table->timestamps();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
            $table->softDeletes();

            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('updatedByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('branchId')
                ->references('id')->on('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

    }
}
