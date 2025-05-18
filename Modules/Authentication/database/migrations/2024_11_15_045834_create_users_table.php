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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique()->index();
            $table->string('phone')->unique()->index();
            $table->string('username')->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('firstName')->index();
            $table->string('lastName')->index();
            $table->string('dob')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->timestamp('two_factor_enabled_at')->nullable();
            $table->boolean('two_fa_status')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
