<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'shopee_product_id' => 'SP-TENIS-001',
                'name'              => 'Tênis Feminino Plataforma Slip On Casual Confortável Moda',
                'category'          => 'Calçados',
                'price'             => 89.90,
                'commission_rate'   => 12.5,
                'rating'            => 4.8,
                'sales_count'       => 8420,
                'opportunity_score' => 82.0,
                'status'            => 'active',
            ],
            [
                'shopee_product_id' => 'SP-SKIN-002',
                'name'              => 'Kit Skincare Vitamina C Sérum Facial Anti-idade Hidratante',
                'category'          => 'Beleza e Cuidados',
                'price'             => 67.90,
                'commission_rate'   => 15.0,
                'rating'            => 4.7,
                'sales_count'       => 5130,
                'opportunity_score' => 76.0,
                'status'            => 'active',
            ],
            [
                'shopee_product_id' => 'SP-SUPORTE-003',
                'name'              => 'Suporte Celular Veicular Magnético 360 Graus Universal',
                'category'          => 'Acessórios para Veículos',
                'price'             => 34.90,
                'commission_rate'   => 10.0,
                'rating'            => 4.6,
                'sales_count'       => 12300,
                'opportunity_score' => 68.0,
                'status'            => 'active',
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['shopee_product_id' => $data['shopee_product_id']],
                $data
            );
        }
    }
}
