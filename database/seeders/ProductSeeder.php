<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Products;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Facial Cleanser',
                'stock_quantity' => 25,
                'unit_type' => 'bottle',
                'price' => 15.00,
                'low_stock_threshold' => 5,
                'description' => 'Gentle facial cleanser for all skin types'
            ],
            [
                'name' => 'Anti-Aging Serum',
                'stock_quantity' => 30,
                'unit_type' => 'vial',
                'price' => 45.00,
                'low_stock_threshold' => 3,
                'description' => 'Premium anti-aging serum with vitamins'
            ],
            [
                'name' => 'Moisturizing Cream',
                'stock_quantity' => 12,
                'unit_type' => 'bottle',
                'price' => 25.00,
                'low_stock_threshold' => 5,
                'description' => 'Hydrating cream for dry skin'
            ],
            [
                'name' => 'Exfoliating Scrub',
                'stock_quantity' => 8,
                'unit_type' => 'bottle',
                'price' => 20.00,
                'low_stock_threshold' => 5,
                'description' => 'Gentle exfoliating scrub for smooth skin'
            ],
            [
                'name' => 'Face Mask Set',
                'stock_quantity' => 15,
                'unit_type' => 'box',
                'price' => 35.00,
                'low_stock_threshold' => 3,
                'description' => 'Set of 5 different face masks'
            ],
            [
                'name' => 'Vitamin C Serum',
                'stock_quantity' => 3, // Low stock example
                'unit_type' => 'vial',
                'price' => 40.00,
                'low_stock_threshold' => 5,
                'description' => 'Brightening vitamin C serum'
            ],
            [
                'name' => 'Eye Cream',
                'stock_quantity' => 18,
                'unit_type' => 'bottle',
                'price' => 30.00,
                'low_stock_threshold' => 4,
                'description' => 'Anti-puffiness eye cream'
            ],
            [
                'name' => 'Sunscreen SPF 50',
                'stock_quantity' => 22,
                'unit_type' => 'bottle',
                'price' => 18.00,
                'low_stock_threshold' => 6,
                'description' => 'High protection sunscreen'
            ]
        ];

        foreach ($products as $product) {
            Products::create($product);
        }
    }
}
