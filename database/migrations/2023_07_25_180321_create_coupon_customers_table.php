<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('couponId');
            $table->unsignedBigInteger('customerId')->nullable();
            $table->string('group')->nullable();
            $table->smallInteger('couponUsage')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('couponId')
                ->references('id')->on('coupons')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('customerId')
                ->references('id')->on('customers')
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
        Schema::dropIfExists('coupon_customers');
    }
}
