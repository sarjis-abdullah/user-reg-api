<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenericExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generic_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->string('resource', 128);
            $table->integer('items');
            $table->string('status', 30);
            $table->string('fileName', 256)->nullable();
            $table->string('viewPath', 128)->nullable();
            $table->string('statusMessage', 512)->nullable();
            $table->string('exportAs', 14)->nullable();
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
        Schema::dropIfExists('generic_exports');
    }
}
