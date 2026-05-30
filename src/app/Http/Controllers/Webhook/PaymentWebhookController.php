<?php

namespace App\Http\Controllers\Webhook;

use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\PaymentGatewayManager;
use App\Domain\Wallet\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, string $driver, PaymentGatewayManager $manager)
    {
        $gateway = Gateway::where('driver', $driver)->firstOrFail();
        $paymentDriver = $manager->driver($driver);

        $rawPayload = $request->getContent();
        $headers = array_change_key_case($request->headers->all(), CASE_LOWER);
        $headers = array_map(fn ($v) => is_array($v) ? ($v[0] ?? '') : $v, $headers);

        try {
            $paymentDriver->verifyWebhookSignature($gateway, $rawPayload, $headers);
        } catch (WebhookSignatureException $e) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = json_decode($rawPayload, true) ?? [];
        $eventType = $payload['type'] ?? $payload['event_type'] ?? 'unknown';
        $eventId = $this->extractEventId($driver, $payload, $headers);

        $existing = DB::table('webhook_events')
            ->where('gateway', $driver)
            ->where('event_id', $eventId)
            ->first();

        if ($existing) {
            return response()->json(['status' => 'already_processed']);
        }

        $webhookEventId = DB::table('webhook_events')->insertGetId([
            'gateway' => $driver,
            'event_type' => $eventType,
            'event_id' => $eventId,
            'payload' => $rawPayload,
            'status' => 'processed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $payment = $paymentDriver->handleWebhook($gateway, $rawPayload, $headers);

            if ($payment && $payment->isCompleted() && ! $payment->transaction_id) {
                $paymentController = app(PaymentController::class);
                $walletService = app(WalletService::class);
                $paymentController->creditWallet($payment, $walletService);
            }
        } catch (\Throwable $e) {
            DB::table('webhook_events')
                ->where('id', $webhookEventId)
                ->update(['status' => 'failed', 'error' => $e->getMessage()]);

            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    private function extractEventId(string $driver, array $payload, array $headers): string
    {
        return match ($driver) {
            'stripe' => $payload['id'] ?? Str::uuid(),
            'paypal' => $payload['id'] ?? Str::uuid(),
            default => Str::uuid(),
        };
    }
}
