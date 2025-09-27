<?php

namespace Modules\OrderManagement\Services;

use Illuminate\Http\Request;
use Modules\OrderManagement\Jobs\StripeWebhookJob;
use Modules\OrderManagement\Models\WebhookRespnse;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;
use UnexpectedValueException;

class StripeWebhookService
{
    protected $secret;

    public function __construct()
    {
        $this->secret = config('OrderManagement.stripe.webhook_secret');
    }

    public function receiveStripeWebhook(Request $request)
    {
        $sigHeader = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        $webhook = WebhookRespnse::create([
            'event_id' => null, // set after verifying
            'type' => 'unknown',
            'raw_payload' => json_encode($payload),
            'payload' => json_decode($payload, true),
            'signature' => $sigHeader,
            'status' => 'pending',
            'received_at' => now(),
        ]);

        try {

            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->secret
            );

            $webhook->update([
                'event_id' => $event->id,
                'type' => $event->type,
                'status' => 'processing',
            ]);

            StripeWebhookJob::dispatch($payload, $webhook->id);

            return response('Webhook handled', 200);
        } catch (UnexpectedValueException $e) {
            $webhook->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            $webhook->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            logError($e);
            return response('Invalid signature', 400);
        } catch (Throwable $e) {
            logError($e);
            $webhook->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return response('Webhook error', 500);
        }
    }
}
