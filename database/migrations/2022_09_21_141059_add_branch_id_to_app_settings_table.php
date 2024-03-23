<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->bigInteger('branchId')->unsigned()->nullable()->after('id');

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
        if (Schema::hasColumn('app_settings', 'branchId'))
        {
            Schema::table('app_settings', function (Blueprint $table)
            {
                $table->dropForeign(['branchId']);
                $table->dropColumn('branchId');
            });
        }
    }
}
