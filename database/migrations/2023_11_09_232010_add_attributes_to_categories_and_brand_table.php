<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributesToCategoriesAndBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('wcCategoryId')->nullable()->after('createdByUserId');
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->integer('wcBrandId')->nullable()->after('createdByUserId');
        });
        Schema::table('taxes', function (Blueprint $table) {
            $table->integer('wcTaxId')->nullable()->after('createdByUserId');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->integer('wcProductId')->nullable()->after('createdByUserId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('wcCategoryId');
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('wcBrandId');
        });
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropColumn('wcTaxId');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('wcProductId');
        });
    }
}
