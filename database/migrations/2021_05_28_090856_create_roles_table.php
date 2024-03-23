<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->string('type');
            $table->string('title');
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

        });

        DB::table('roles')->insert([
            ['id' => 1, 'type' => 'admin', 'title' => 'super_admin'],
            ['id' => 2, 'type' => 'admin', 'title' => 'standard_admin'],
            ['id' => 3, 'type' => 'admin', 'title' => 'limited_admin'],

            ['id' => 4, 'type' => 'manager', 'title' => 'super_manager'],
            ['id' => 5, 'type' => 'manager', 'title' => 'standard_manager'],
            ['id' => 6, 'type' => 'manager', 'title' => 'restricted_manager'],

            ['id' => 7, 'type' => 'employee', 'title' => 'basic_employee'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
