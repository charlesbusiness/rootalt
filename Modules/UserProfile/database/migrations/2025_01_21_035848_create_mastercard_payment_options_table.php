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
        Schema::create('mastercard_payment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')
            ->on('users')
            ->onDelete('CASCADE')
            ->onUpdate('NO ACTION');
            $table->string('names');
            $table->string('cvv');
            $table->string('card_number');
            $table->string('card_expiration');
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mastercard_payment_options');
    }
};
