<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_respnses', function (Blueprint $table) {
            $table->id();

            // Stripe event identifier (evt_xxx) — unique to prevent duplicates
            $table->string('event_id')->unique()->nullable();

            // Event type (payment_intent.succeeded, charge.refunded, etc.)
            $table->string('type')->index();

            // Raw payload exactly as received from stripe (keeps full record)
            $table->longText('raw_payload');

            // Parsed JSON payload (if you want to query JSON fields)
            // Use json column type where supported
            $table->json('payload')->nullable();

            // Signature header value (useful for debugging)
            $table->string('signature')->nullable();

            // Timestamps for handling lifecycle
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();

            // Processing status (pending -> processing -> processed/failed)
            $table->enum('status', ['pending', 'processing', 'processed', 'failed'])
                ->default('pending')
                ->index();

            // HTTP status returned to Stripe (optional)
            $table->unsignedSmallInteger('http_status')->nullable();

            // Error message if processing failed
            $table->text('error_message')->nullable();

            // Retry / attempt counter
            $table->unsignedInteger('attempts')->default(0);

            $table->timestamps();

            // Helpful indexes
            $table->index(['received_at']);
            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_respnses');
    }
};
