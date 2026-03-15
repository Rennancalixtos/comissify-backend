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
}
