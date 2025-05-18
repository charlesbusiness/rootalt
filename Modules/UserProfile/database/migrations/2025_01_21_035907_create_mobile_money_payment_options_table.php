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
        Schema::create('mobile_money_payment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_money_category_id');
             $table->foreignId('user_id')->references('id')
            ->on('users')
            ->onDelete('CASCADE')
            ->onUpdate('NO ACTION');
            $table->string('phone_number');
            $table->string('country_phone_code');
            $table->boolean('is_verified')->default(false);
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_money_payment_options');
    }
};
