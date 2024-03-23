<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferPromoterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('offer_promoter_products', function (Blueprint $table) {
            $table->id();
            $table->float('quantity')->default(1);
            $table->foreignId('productId')
                ->constrained('products', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('bundleId')
                ->constrained('bundles', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('stockId')
                ->nullable()
                ->constrained('stocks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_promoter_products');
    }
}
