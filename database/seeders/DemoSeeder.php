<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAffiliateLink;
use App\Models\UserCredential;
use App\Models\UserPost;
use App\Models\UserTelegramGroup;
use App\Models\UserToken;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Dados do modo demonstração: um usuário já conectado à "Shopee",
 * com saldo, links gerados, posts prontos e grupos do Telegram.
 *
 * Uso: php artisan migrate:fresh --force --seeder=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Pacotes, preços e conta padrão do sistema
        $this->call(DatabaseSeeder::class);

        $user = User::updateOrCreate(
            ['email' => config('demo.email')],
            ['name' => 'Visitante Demo', 'password' => Hash::make(config('demo.password'))]
        );
        $user->forceFill(['is_admin' => false, 'is_active' => true, 'email_verified_at' => now()])->save();

        UserCredential::updateOrCreate(['user_id' => $user->id], [
            'app_id'       => 'demo-app-id',
            'app_id_hash'  => hash('sha256', 'demo-app-id'),
            'app_secret'   => Str::random(32),
            'affiliate_id' => 'demo',
            'is_connected' => true,
            'connected_at' => now(),
        ]);

        UserToken::updateOrCreate(['user_id' => $user->id], [
            'balance'          => 250,
            'reports_unlocked' => true,
        ]);

        $products = DemoCatalog::all();

        // Links já gerados (aparecem no Criador de posts)
        $links = [];
        foreach (array_slice($products, 0, 6) as $p) {
            $links[] = UserAffiliateLink::updateOrCreate(
                ['user_id' => $user->id, 'item_id' => (string) $p['itemId']],
                [
                    'product_name'    => $p['productName'],
                    'image_url'       => $p['imageUrl'],
                    'product_link'    => $p['productLink'],
                    'affiliate_link'  => 'https://s.shopee.com.br/demo' . substr(md5($p['productLink']), 0, 7),
                    'price_min'       => (string) $p['priceMin'],
                    'commission_rate' => (string) $p['commissionRate'],
                    'shop_name'       => $p['shopName'],
                ]
            );
        }

        foreach (array_slice($links, 0, 3) as $link) {
            $price = number_format((float) $link->price_min, 2, ',', '.');
            UserPost::create([
                'user_id'           => $user->id,
                'affiliate_link_id' => $link->id,
                'type'              => 'text',
                'status'            => 'completed',
                'credits_charged'   => 0,
                'result'            => [
                    'title'       => "🔥 {$link->product_name}",
                    'description' => "Achado do dia! {$link->product_name} por apenas R$ {$price} na loja {$link->shop_name}. "
                                   . "Milhares de avaliações positivas e entrega para todo o Brasil. Corre que o estoque é limitado! 👇\n{$link->affiliate_link}",
                    'image_url'   => $link->image_url,
                ],
            ]);
        }

        UserTelegramGroup::updateOrCreate(
            ['user_id' => $user->id, 'chat_id' => -1001000000001],
            ['chat_title' => 'Ofertas Relâmpago 🔥']
        );
        UserTelegramGroup::updateOrCreate(
            ['user_id' => $user->id, 'chat_id' => -1001000000002],
            ['chat_title' => 'Achadinhos da Shopee']
        );

        // Páginas públicas de tendências (normalmente preenchidas pelo trending:fetch)
        $bySales = collect($products)->sortByDesc('sales')->values()->all();
        $byComm  = collect($products)->sortByDesc('commission')->values()->all();
        Cache::forever('trending_products', $bySales);
        Cache::forever('commission_products', $byComm);
        Cache::forever('trending_products_updated_at', now()->toIso8601String());
    }
}
