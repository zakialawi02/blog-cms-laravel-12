<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $products = [
            [
                'product_name'   => 'Laptop Gaming Super',
                'slug'           => 'laptop-gaming-super',
                'description'    => 'Laptop gaming high-end dengan performa terbaik.',
                'price'          => 700,
                'discount_price' => 680,
                'thumbnail'      => 'laptop.jpg',
                'stock'          => 10,
                'sales_count'    => 5,
            ],
            [
                'product_name'   => 'Desktop Gaming Pro',
                'slug'           => 'desktop-gaming-pro',
                'description'    => 'Desktop gaming dengan spesifikasi tinggi untuk gaming profesional.',
                'price'          => 1200,
                'discount_price' => 1150,
                'thumbnail'      => 'desktop.jpg',
                'stock'          => 8,
                'sales_count'    => 3,
            ],
            [
                'product_name'   => 'Monitor 4K Ultra',
                'slug'           => 'monitor-4k-ultra',
                'description'    => 'Monitor 4K dengan kualitas gambar yang sempurna.',
                'price'          => 450,
                'discount_price' => 420,
                'thumbnail'      => 'monitor.jpg',
                'stock'          => 15,
                'sales_count'    => 8,
            ],
            [
                'product_name'   => 'Mechanical Keyboard RGB',
                'slug'           => 'mechanical-keyboard-rgb',
                'description'    => 'Keyboard mekanik dengan lampu RGB yang dapat dikustomisasi.',
                'price'          => 150,
                'discount_price' => 135,
                'thumbnail'      => 'keyboard.jpg',
                'stock'          => 25,
                'sales_count'    => 12,
            ],
            [
                'product_name'   => 'Gaming Mouse Wireless',
                'slug'           => 'gaming-mouse-wireless',
                'description'    => 'Mouse gaming wireless dengan sensor presisi tinggi.',
                'price'          => 80,
                'discount_price' => 75,
                'thumbnail'      => 'mouse.jpg',
                'stock'          => 30,
                'sales_count'    => 15,
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'id'             => Str::uuid(),
                'user_id'        => User::where('role', '!=', 'superadmin')->inRandomOrder()->first()->id,
                'product_name'   => $product['product_name'],
                'slug'           => $product['slug'],
                'description'    => $product['description'],
                'currency'       => 'USD',
                'price'          => $product['price'],
                'discount_price' => $product['discount_price'],
                'thumbnail'      => $product['thumbnail'],
                'stock'          => $product['stock'],
                'is_published'   => true,
                'sales_count'    => $product['sales_count'],
            ]);
        }
    }
}
