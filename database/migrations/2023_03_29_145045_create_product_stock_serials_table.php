<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockSerialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productId')
                ->constrained('products', 'id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('stockId')
                ->constrained('stocks', 'id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('productStockSerialId', 100);

            $table->foreignId('createdByUserId')
                ->nullable()
                ->constrained('users', 'id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('updatedByUserId')
                ->nullable()
                ->constrained('users', 'id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('status')->nullable();

            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stock_serials');
    }
}
