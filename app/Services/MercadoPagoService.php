<?php

namespace App\Services;

use App\Models\PaymentOrder;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Cria uma preferência no MP e retorna ['preference_id' => '...'].
     */
    public function createPreference(PaymentOrder $order): array
    {
        $client = new PreferenceClient();

        $preference = $client->create([
            'items' => [
                [
                    'id'          => (string) $order->package_id,
                    'title'       => $order->package->name,
                    'quantity'    => 1,
                    'unit_price'  => (float) $order->amount,
                    'currency_id' => 'BRL',
                ],
            ],
            'payer' => [
                'email' => $order->user->email,
            ],
            'payment_methods' => [
                'excluded_payment_types' => [
                    ['id' => 'ticket'],  // boleto
                    ['id' => 'atm'],     // caixa eletrônico
                ],
                'installments' => 1,
            ],
            'back_urls' => [
                'success' => route('payment.success'),
                'failure' => route('payment.failure'),
                'pending' => route('payment.pending'),
            ],
            'auto_return'          => 'approved',
            'external_reference'   => (string) $order->id,
            'notification_url'     => route('payment.webhook'),
            'statement_descriptor' => 'Comissify',
        ]);

        return ['preference_id' => $preference->id];
    }
}
