<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Bibit Jagung Premium',
                'description' => 'Bibit jagung berkualitas tinggi, tumbuh subur dalam 14 hari. Cocok untuk lahan kering dan basah.',
                'price' => 25000,
                'type' => 'sale',
                'image' => 'products/jagung.jpg',
                'category' => 'Benih',
                'stock' => 100,
                'is_active' => true
            ],
            [
                'name' => 'Pupuk Organik',
                'description' => 'Pupuk organik 5kg, terbuat dari bahan alami berkualitas tinggi. Cocok untuk berbagai jenis tanaman.',
                'price' => 75000,
                'type' => 'sale',
                'image' => 'products/pupuk.jpg',
                'category' => 'Pupuk',
                'stock' => 50,
                'is_active' => true
            ],
            [
                'name' => 'Traktor Mini',
                'description' => 'Traktor mini untuk lahan kecil dan menengah. Mudah dioperasikan dan hemat bahan bakar.',
                'price' => 250000,
                'type' => 'rent',
                'rent_period' => 'per minggu',
                'image' => 'products/traktor.jpg',
                'category' => 'Alat Berat',
                'stock' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Alat Penyiram Otomatis',
                'description' => 'Sistem penyiraman otomatis dengan timer dan sensor kelembapan. Cocok untuk greenhouse dan kebun.',
                'price' => 125000,
                'type' => 'rent',
                'rent_period' => 'per bulan',
                'image' => 'products/penyiram.png',
                'category' => 'Irigasi',
                'stock' => 10,
                'is_active' => true
            ],
            [
                'name' => 'Alat Ukur pH Tanah',
                'description' => 'Alat ukur pH tanah digital dengan akurasi tinggi. Penting untuk monitoring kualitas tanah.',
                'price' => 150000,
                'type' => 'rent',
                'rent_period' => 'per minggu',
                'image' => 'products/ph-meter.png',
                'category' => 'Alat Ukur',
                'stock' => 8,
                'is_active' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 