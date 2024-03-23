<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OtpManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class, 'userId');
            $table->string('code');
            $table->dateTime('expireAt');
            $table->string('type')->default('verify_phone');
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
        Schema::dropIfExists('otp_managers');
    }
}
