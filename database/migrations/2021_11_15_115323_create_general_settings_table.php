<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_settings');
    }
}
