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
                'key' => 'original',
                'name' => 'Original',
                'description' => 'Rasa natural & gurih. Tipis, renyah.',
                'price' => 15000,
                'image_path' => 'assets/variants/original.jpg',
                'glow_color' => 'rgba(255,213,74,.22)',
                'sort_order' => 1,
            ],
            [
                'key' => 'keju',
                'name' => 'Keju',
                'description' => 'Aroma keju creamy, balance manis.',
                'price' => 15000,
                'image_path' => 'assets/variants/keju.jpg',
                'glow_color' => 'rgba(255,255,255,.18)',
                'sort_order' => 2,
            ],
            [
                'key' => 'balado',
                'name' => 'Balado',
                'description' => 'Pedas-manis gurih, bikin nagih.',
                'price' => 15000,
                'image_path' => 'assets/variants/balado.jpg',
                'glow_color' => 'rgba(255,120,60,.20)',
                'sort_order' => 3,
            ],
            [
                'key' => 'coklat',
                'name' => 'Coklat',
                'description' => 'Manis coklat tebal, cocok dessert.',
                'price' => 17000,
                'image_path' => 'assets/variants/coklat.jpg',
                'glow_color' => 'rgba(120,75,45,.22)',
                'sort_order' => 4,
            ],
            [
                'key' => 'pedas',
                'name' => 'Pedas',
                'description' => 'Lebih nendang untuk pecinta pedas.',
                'price' => 15000,
                'image_path' => 'assets/variants/pedas.jpg',
                'glow_color' => 'rgba(255,59,92,.18)',
                'sort_order' => 5,
            ],
            [
                'key' => 'pedas2',
                'name' => 'Pedas Level 2',
                'description' => 'Extra nendang buat yang berani.',
                'price' => 15000,
                'image_path' => 'assets/variants/pedas.jpg',
                'glow_color' => 'rgba(255,59,92,.22)',
                'sort_order' => 6,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['key' => $product['key']],
                $product
            );
        }
    }
}
