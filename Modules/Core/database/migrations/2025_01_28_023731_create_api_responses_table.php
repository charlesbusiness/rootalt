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
        Schema::create('api_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_request_id');
            $table->string('response_message')->nullable();
            $table->string('response_status_code')->nullable();
            $table->json('response_data')->nullable();
            $table->string('provider')->nullable();
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_responses');
    }
};
