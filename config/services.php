<?php

return [

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'pinterest' => [
        'scraper_key' => env('PINTEREST_SCRAPER_KEY'),
    ],

    'shopee' => [
        'app_id'       => env('SHOPEE_APP_ID'),
        'secret'       => env('SHOPEE_SECRET'),
        'affiliate_id' => env('SHOPEE_AFFILIATE_ID'),
        'api_base_url' => env('SHOPEE_API_BASE_URL', 'https://open-api.affiliate.shopee.com.br/graphql'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model'   => env('ANTHROPIC_MODEL', 'claude-haiku-4-5-20251001'),
    ],

    'mercadopago' => [
        'access_token' => env('MP_ACCESS_TOKEN'),
        'public_key'   => env('MP_PUBLIC_KEY'),
        'webhook_secret' => env('MP_WEBHOOK_SECRET'), // para validar assinatura do webhook
    ],

    'appium' => [
        'server_url'     => env('APPIUM_SERVER_URL', 'http://localhost:4723'),
        'implicit_wait'  => env('APPIUM_IMPLICIT_WAIT', 10000),
        'platform_name'  => env('APPIUM_PLATFORM_NAME', 'Android'),
        'device_name'    => env('APPIUM_DEVICE_NAME', ''),
        'app_package'    => env('APPIUM_APP_PACKAGE', 'com.shopee.id'),
        'app_activity'   => env('APPIUM_APP_ACTIVITY', 'com.shopee.id.MainActivity'),
    ],

];
