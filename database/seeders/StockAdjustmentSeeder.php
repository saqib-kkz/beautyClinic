<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock_adjustments;
use App\Models\Products;
use App\Models\User;

class StockAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Products::all();
        $admin = User::where('role', 'admin')->first();
        $staff = User::where('role', 'staff')->first();

        $adjustments = [
            // Restocking
            [
                'product_id' => $products[0]->id, // Facial Cleanser
                'user_id' => $admin->id,
                'previous_quantity' => 15,
                'adjusted_quantity' => 25,
                'difference' => 10,
                'adjustment_type' => 'manual_increase',
                'reason' => 'New shipment received from supplier',
            ],
            [
                'product_id' => $products[1]->id, // Anti-Aging Serum
                'user_id' => $admin->id,
                'previous_quantity' => 20,
                'adjusted_quantity' => 30,
                'difference' => 10,
                'adjustment_type' => 'manual_increase',
                'reason' => 'Monthly restock order',
            ],
            // Treatment usage
            [
                'product_id' => $products[0]->id, // Facial Cleanser
                'user_id' => $staff->id,
                'previous_quantity' => 28,
                'adjusted_quantity' => 25,
                'difference' => -3,
                'adjustment_type' => 'treatment_usage',
                'reason' => 'Used in facial treatments',
            ],
            [
                'product_id' => $products[3]->id, // Exfoliating Scrub
                'user_id' => $staff->id,
                'previous_quantity' => 10,
                'adjusted_quantity' => 8,
                'difference' => -2,
                'adjustment_type' => 'treatment_usage',
                'reason' => 'Used in HydraFacial treatment',
            ],
            // Damaged goods
            [
                'product_id' => $products[5]->id, // Vitamin C Serum
                'user_id' => $admin->id,
                'previous_quantity' => 5,
                'adjusted_quantity' => 3,
                'difference' => -2,
                'adjustment_type' => 'damaged_goods',
                'reason' => 'Two vials broken during storage reorganization',
            ],
            // Expired goods
            [
                'product_id' => $products[4]->id, // Face Mask Set
                'user_id' => $admin->id,
                'previous_quantity' => 18,
                'adjusted_quantity' => 15,
                'difference' => -3,
                'adjustment_type' => 'expired_goods',
                'reason' => 'Three boxes past expiration date - disposed',
            ],
            // Stock correction
            [
                'product_id' => $products[6]->id, // Eye Cream
                'user_id' => $admin->id,
                'previous_quantity' => 20,
                'adjusted_quantity' => 18,
                'difference' => -2,
                'adjustment_type' => 'stock_correction',
                'reason' => 'Physical count mismatch - adjusted to match actual stock',
            ],
            // Manual decrease
            [
                'product_id' => $products[7]->id, // Sunscreen SPF 50
                'user_id' => $staff->id,
                'previous_quantity' => 25,
                'adjusted_quantity' => 22,
                'difference' => -3,
                'adjustment_type' => 'manual_decrease',
                'reason' => 'Sample units given to clients',
            ],
        ];

        foreach ($adjustments as $adjustment) {
            Stock_adjustments::create($adjustment);
        }
    }
}
