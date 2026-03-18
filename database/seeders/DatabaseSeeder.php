<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\SystemPrice;
use App\Models\TokenPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name'      => 'Admin',
                'password'  => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'is_admin'  => true,
                'is_active' => true,
            ]
        );

        Account::updateOrCreate(
            ['shopee_username' => 'shopee_affiliate_main'],
            [
                'name'             => 'Conta Principal',
                'daily_post_limit' => 10,
                'status'           => 'active',
            ]
        );

        TokenPackage::updateOrCreate(['name' => 'Starter'], [
            'tokens_amount' => 50,
            'price'         => 9.90,
            'is_active'     => true,
            'is_featured'   => false,
        ]);
        TokenPackage::updateOrCreate(['name' => 'Pro'], [
            'tokens_amount' => 150,
            'price'         => 24.90,
            'is_active'     => true,
            'is_featured'   => true,
        ]);
        TokenPackage::updateOrCreate(['name' => 'Enterprise'], [
            'tokens_amount' => 500,
            'price'         => 69.90,
            'is_active'     => true,
            'is_featured'   => false,
        ]);

        SystemPrice::updateOrCreate(['key' => 'search_cost'], [
            'value'       => 0.020000,
            'description' => 'Custo por busca de produtos',
        ]);
        SystemPrice::updateOrCreate(['key' => 'link_generation_cost'], [
            'value'       => 0.010000,
            'description' => 'Custo por link afiliado gerado',
        ]);
        SystemPrice::updateOrCreate(['key' => 'post_text_cost'], [
            'value'       => 0.200000,
            'description' => 'Custo por Texto AI após as 10 gerações gratuitas diárias',
        ]);
        SystemPrice::updateOrCreate(['key' => 'post_image_cost'], [
            'value'       => 0.990000,
            'description' => 'Custo por Imagem com IA (Pollinations)',
        ]);
        SystemPrice::updateOrCreate(['key' => 'post_video_cost'], [
            'value'       => 1.990000,
            'description' => 'Custo por Vídeo de Venda',
        ]);
        SystemPrice::updateOrCreate(['key' => 'post_pinterest_cost'], [
            'value'       => 0.100000,
            'description' => 'Custo por busca no Pinterest',
        ]);
        SystemPrice::updateOrCreate(['key' => 'post_telegram_cost'], [
            'value'       => 0.010000,
            'description' => 'Custo por envio Telegram (por grupo)',
        ]);

        \App\Models\Setting::updateOrCreate(['key' => 'theme_color'], [
            'value'       => 'purple',
            'description' => 'Tema de cor da plataforma (purple|green|blue|orange)',
        ]);

        \App\Models\LandingScreenshot::updateOrCreate(['order' => 1], ['title' => 'Dashboard de produtos',    'image_path' => 'landing/placeholder-1.png', 'order' => 1, 'is_active' => true]);
        \App\Models\LandingScreenshot::updateOrCreate(['order' => 2], ['title' => 'Geração de links em lote', 'image_path' => 'landing/placeholder-2.png', 'order' => 2, 'is_active' => true]);
        \App\Models\LandingScreenshot::updateOrCreate(['order' => 3], ['title' => 'Relatório de comissões',   'image_path' => 'landing/placeholder-3.png', 'order' => 3, 'is_active' => true]);

        $this->call(ProductSeeder::class);
    }
}
