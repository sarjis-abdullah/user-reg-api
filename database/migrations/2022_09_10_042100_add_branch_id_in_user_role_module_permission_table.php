<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdInUserRoleModulePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_role_module_permissions', function (Blueprint $table) {
            $table->bigInteger('branchId')->unsigned()->nullable()->after('createdByUserId');

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
        Schema::table('user_role_module_permissions', function (Blueprint $table) {
        });
    }
}
