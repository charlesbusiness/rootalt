<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_managers', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');

            // Identifiers
            $table->string('order_number')->unique();
            $table->string('transaction_id')->nullable();
            $table->year('year')->index();

            // Totals
            $table->unsignedInteger('total_qty')->default(0);
            $table->unsignedInteger('payment_intent_id');
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('tax', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);

            // Statuses
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned', 'refunded'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('shipping_status', ['not_shipped', 'shipped', 'delivered', 'failed'])->default('not_shipped');

            // Extras
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_managers');
    }
};
