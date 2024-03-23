<?php

use App\Models\Bundle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('customerBuys')->default(Bundle::CUSTOMER_BUYS_PRODUCTS);
            $table->string('customerGets')->default(Bundle::CUSTOMER_GETS_PRODUCTS); // items or value
            $table->enum('offerCombinesWith', Bundle::OFFER_COMBINES_WITH)->nullable();
            $table->string('eligibleCustomerType')->default(Bundle::CUSTOMER_ELIGIBLE_TYPE);
            $table->unsignedInteger('usesPerOrderLimit')->nullable();
            $table->unsignedInteger('usesPerUserLimit')->nullable();
            $table->unsignedInteger('usageLimit')->nullable();
            $table->dateTime('offerStartsAt');
            $table->dateTime('offerEndsAt')->nullable();
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
        Schema::dropIfExists('bundles');
    }
}
