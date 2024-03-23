<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteResourceAttributeFromGenericExport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('generic_exports', function (Blueprint $table) {
            $table->dropColumn('resource');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('generic_exports', function (Blueprint $table) {
            $table->string('resource', 128)->after('createdByUserId');
        });
    }
}
