<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        if (!$this->verifySignature($request)) {
            Log::warning('MP Webhook: assinatura inválida', ['ip' => $request->ip()]);
            return response('unauthorized', 401);
        }

        // MP envia type=payment para notificações de pagamento
        if ($request->query('type') !== 'payment') {
            return response('ok', 200);
        }

        $paymentId = $request->query('data_id') ?? $request->input('data.id');

        if (!$paymentId) {
            return response('missing payment id', 400);
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

            $client  = new PaymentClient();
            $payment = $client->get((int) $paymentId);

            $orderId = $payment->external_reference;
            $status  = $payment->status;
            $amount  = (float) $payment->transaction_amount;

            Log::info("MP Webhook [payment:{$paymentId}] status:{$status} order:{$orderId}");

            $order = PaymentOrder::find($orderId);

            if (!$order) {
                Log::warning("MP Webhook: order {$orderId} not found");
                return response('order not found', 404);
            }

            // Já processado — idempotência
            if ($order->isApproved()) {
                return response('already processed', 200);
            }

            // Validação de segurança: valor pago deve bater com o da ordem
            if (abs($amount - (float) $order->amount) > 0.01) {
                Log::error("MP Webhook: amount mismatch. Expected {$order->amount}, got {$amount}. Order {$orderId}");
                return response('amount mismatch', 422);
            }

            $order->update([
                'mp_payment_id' => (string) $paymentId,
                'status'        => $status,
                'paid_at'       => $status === 'approved' ? now() : null,
            ]);

            if ($status === 'approved') {
                $wallet = $order->user->ensureTokenWallet();
                $wallet->credit(
                    (float) $order->tokens_amount,
                    "Compra de créditos — {$order->package->name}"
                );

                Log::info("MP Webhook: credited {$order->tokens_amount} tokens to user {$order->user_id}");
            }

            return response('ok', 200);
        } catch (\Throwable $e) {
            Log::error("MP Webhook error: {$e->getMessage()}", ['payment_id' => $paymentId]);
            return response('error', 500);
        }
    }

    /**
     * Verifica a assinatura HMAC-SHA256 do webhook MercadoPago (V2).
     * Documentação: https://www.mercadopago.com.br/developers/pt/docs/your-integrations/notifications/webhooks
     *
     * Header x-signature: "ts=TIMESTAMP,v1=HASH"
     * Payload assinado:   "id:{data_id};request-id:{x-request-id};ts:{ts}"
     */
    private function verifySignature(Request $request): bool
    {
        $secret = config('services.mercadopago.webhook_secret');

        // Se não há secret configurado, bloqueia (fail-safe)
        if (!$secret) {
            Log::error('MP Webhook: MP_WEBHOOK_SECRET não configurado.');
            return false;
        }

        $xSignature = $request->header('x-signature', '');
        $xRequestId = $request->header('x-request-id', '');
        // MP envia data.id na query string (PHP converte para data_id) OU no body JSON
        $dataId     = (string) ($request->query('data_id') ?? $request->json('data.id') ?? $request->json('id') ?? '');

        // Extrai ts e v1 do header x-signature
        $ts   = '';
        $hash = '';
        foreach (explode(',', $xSignature) as $part) {
            [$key, $val] = array_pad(explode('=', $part, 2), 2, '');
            if ($key === 'ts')  $ts   = $val;
            if ($key === 'v1')  $hash = $val;
        }

        if (!$ts || !$hash) {
            return false;
        }

        $signed   = "id:{$dataId};request-id:{$xRequestId};ts:{$ts}";
        $expected = hash_hmac('sha256', $signed, $secret);

        return hash_equals($expected, $hash);
    }
}
