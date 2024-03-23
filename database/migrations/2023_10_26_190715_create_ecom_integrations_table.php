<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcomIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecom_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('branchId');
            $table->string('name', 56);
            $table->string('apiUrl', 512);
            $table->string('apiKey', 256);
            $table->string('apiSecret', 256);
            $table->unsignedBigInteger('updatedByUserId')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecom_integrations');
    }
}
