<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('createdByUserId')->nullable();
            $table->string('title', 128);
            $table->string('code', 16);
            $table->string('description', 256)->nullable();
            $table->string('to', 128);
            $table->string('type', 128);
            $table->float('amount');
            $table->string('amountType');
            $table->float('minTxnAmount')->default(0)->nullable();
            $table->float('maxDiscountAmount')->default(0)->nullable();
            $table->string('usedIn')->default(\App\Models\Coupon::USED_IN_POS)->nullable();
            $table->dateTime('startDate')->nullable();
            $table->dateTime('expirationDate')->nullable();
            $table->unsignedBigInteger('updatedByUserId')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
