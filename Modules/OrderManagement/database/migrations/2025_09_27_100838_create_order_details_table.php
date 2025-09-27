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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('order_manager_id')
                ->constrained('order_managers')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict'); // or cascade depending on business rules

            // Order item details
            $table->unsignedInteger('qty'); // prevent negative numbers
            $table->decimal('cost_per_item', 10, 2);
            $table->decimal('line_total', 12, 2)->nullable(); // qty * cost_per_item

            // Optional extra fields
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('USD'); // ISO currency

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
