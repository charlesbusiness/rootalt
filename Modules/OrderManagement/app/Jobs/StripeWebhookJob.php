<?php

namespace Modules\OrderManagement\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\OrderManagement\Models\WebhookRespnse;
use Modules\OrderManagement\Services\StripeService;
use Stripe\Event;
use Throwable;

class StripeWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $event;
    protected $webhookId;

    public function __construct($event, int $webhookId)
    {
        // Store as array to avoid serialization issues
        $this->event = $event instanceof Event ? $event->toArray() : $event;
        $this->webhookId = $webhookId;
    }


    /**
     * Execute the job.
     */
    public function handle(StripeService $stripeService)
    {
        $webhook = WebhookRespnse::find($this->webhookId);
        if (!$webhook) return;

        try {
            $event = new Event($this->event);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $stripeService->markOrderAsPaid($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $stripeService->markOrderAsFailed($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    $stripeService->cancelOrder($event->data->object);
                    break;

                case 'charge.refunded':
                    $stripeService->processRefund($event->data->object);
                    break;

                default:
                    Log::info("Unhandled Stripe event: {$event->type}");
                    break;
            }

            $webhook->update([
                'status' => 'processed',
                'processed_at' => now(),
                'attempts' => $webhook->attempts + 1,
                'http_status' => 200,
            ]);
        } catch (Throwable $e) {
            $webhook->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'attempts' => $webhook->attempts + 1,
            ]);
            throw $e; // let queue retry
        }
    }
}
