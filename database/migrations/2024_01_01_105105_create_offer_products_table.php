<?php

use App\Models\OfferProduct;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_products', function (Blueprint $table) {
            $table->id();
            $table->float('quantity')->default(1);
            $table->foreignId('productId')
                ->constrained('products', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('discountType')->default(OfferProduct::DISCOUNT_TYPE_FREE);
            $table->double('discountAmount')->nullable();
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
        Schema::dropIfExists('offer_products');
    }
}
