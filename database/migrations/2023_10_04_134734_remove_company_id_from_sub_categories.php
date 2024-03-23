<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCompanyIdFromSubCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropForeign('sub_categories_companyid_foreign');
            $table->dropColumn('companyId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('companyId')->nullable()->after('createdByUserId');

            $table->foreign('companyId')
                ->references('id')->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
