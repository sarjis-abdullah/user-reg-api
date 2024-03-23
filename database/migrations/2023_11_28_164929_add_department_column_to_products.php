<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentColumnToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('departmentId')
                ->nullable()
                ->constrained('departments', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('subDepartmentId')
                ->nullable()
                ->constrained('sub_departments', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('departmentId');
            $table->dropColumn('subDepartmentId');
        });
    }
}
