<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AltUserRoleModulePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_role_module_permissions', function (Blueprint $table) {
            $table->json('moduleActionIds')->nullable()->change();
            $table->json('moduleActionNames')->nullable()->after('moduleActionIds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        // second, make nullable
        if (Schema::hasColumn('user_role_module_permissions', 'moduleActionIds')) {
            Schema::table('user_role_module_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('moduleActionIds')->nullable(false)->change();
            });
            Schema::table('user_role_module_permissions', function (Blueprint $table) {
                $table->dropColumn('moduleActionNames');
            });

        }
    }
}
