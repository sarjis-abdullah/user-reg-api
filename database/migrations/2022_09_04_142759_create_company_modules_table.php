<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_modules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('createdByUserId')->unsigned()->nullable();
            $table->bigInteger('companyId')->unsigned()->nullable();
            $table->bigInteger('moduleId')->unsigned()->nullable();
            $table->boolean('isActive')->default(1);
            $table->dateTime('activationDate')->nullable();
            $table->bigInteger('updatedByUserId')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('moduleId')
                ->references('id')->on('modules')
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
        Schema::dropIfExists('company_modules');
    }
}
