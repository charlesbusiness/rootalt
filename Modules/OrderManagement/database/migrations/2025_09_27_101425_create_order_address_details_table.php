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
        Schema::create('order_address_details', function (Blueprint $table) {
            $table->id();

            // Relation to order manager / order
            $table->foreignId('order_manager_id')->constrained('order_managers')->onDelete('cascade');

            // Address fields
            $table->string('full_name');                     // John Doe
            $table->string('phone', 20)->nullable();         // +1 (555) 123-4567
            $table->string('email')->nullable();             // optional contact

            $table->string('address_line1');                 // Street address
            $table->string('address_line2')->nullable();     // Apt, Suite, Unit, etc.
            $table->string('city');                          // City
            $table->string('state', 2);                      // US state code (CA, NY, TX, etc.)
            $table->string('postal_code', 10);               // ZIP or ZIP+4
            $table->string('country', 2)->default('US');     // ISO country code (default US)

            // Type of address
            $table->enum('address_type', ['billing', 'shipping'])->default('shipping');

            // Extra options
            $table->boolean('is_default')->default(false);   // Mark default address for customer

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_address_details');
    }
};
