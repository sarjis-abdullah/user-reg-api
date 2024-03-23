<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatedTablesForUserNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pref_notification_type')->after('remember_token')->default('instant'); // instant, scheduled, in-app
            $table->string('pref_notification_time')->nullable()->after('pref_notification_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pref_notification_type');
            $table->dropColumn('pref_notification_time');
        });
    }
}
