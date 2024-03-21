<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_managers');
    }
};
