<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCompanyIdAndBranchIdIssueInManagersAndEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->foreignId('companyId')->nullable()->change();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('companyId')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // first, drop foreign key
        Schema::table('managers', function (Blueprint $table) {
//            $table->dropForeign(['orderId']);
            $table->dropForeign('managers_companyid_foreign');
        });

        // second, make nullable
        Schema::table('managers', function (Blueprint $table) {
            $table->unsignedBigInteger('companyId')->nullable(false)->change();
        });

        // third, add foreign key again
        Schema::table('managers', function (Blueprint $table) {
            $table->foreign('companyId')
                ->references('id')->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // first, drop foreign key
        Schema::table('employees', function (Blueprint $table) {
//            $table->dropForeign(['orderId']);
            $table->dropForeign('employees_companyid_foreign');
        });

        // second, make nullable
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('companyId')->nullable(false)->change();
        });

        // third, add foreign key again
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('companyId')
                ->references('id')->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
