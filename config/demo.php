<?php

// Modo demonstração: usado só para exibir o Comissify no portfólio.
// Troca as integrações externas (Shopee, Mercado Pago, Telegram) por dados de exemplo
// e reseta o banco a cada inicialização do container.
return [
    'enabled'  => (bool) env('DEMO_MODE', false),
    'email'    => env('DEMO_EMAIL', 'demo@comissify.shop'),
    'password' => env('DEMO_PASSWORD', 'demo1234'),
];
