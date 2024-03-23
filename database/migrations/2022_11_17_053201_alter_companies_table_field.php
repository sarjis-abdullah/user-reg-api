<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCompaniesTableField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('phone', 20)->nullable()->change();
            $table->string('type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone', 20)->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
        });
    }
}
