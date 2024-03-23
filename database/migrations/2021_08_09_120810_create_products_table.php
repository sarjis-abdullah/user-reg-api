<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->unsignedBigInteger('companyId')->nullable();
            $table->unsignedBigInteger('categoryId')->nullable();
            $table->unsignedBigInteger('subCategoryId')->nullable();
            $table->unsignedBigInteger('brandId')->nullable();
            $table->unsignedBigInteger('unitId')->nullable();
            $table->unsignedBigInteger('discountId')->nullable();
            $table->string('name');
            $table->string('barcode')->nullable()->unique();
            $table->float('tax')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('updatedByUserId')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('createdByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('updatedByUserId')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('companyId')
                ->references('id')->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('categoryId')
                ->references('id')->on('categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('subCategoryId')
                ->references('id')->on('sub_categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('brandId')
                ->references('id')->on('brands')
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
        Schema::dropIfExists('products');
    }
}
