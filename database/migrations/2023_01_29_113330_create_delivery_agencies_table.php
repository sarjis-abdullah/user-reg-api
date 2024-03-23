<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDeliveryAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('contactPerson')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('updatedByUserId')->nullable();

            $table->foreign('createdByUserId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updatedByUserId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        DB::table('delivery_agencies')->insert([
            ['name' => 'In House'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_agencies');
    }
}
