<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Treatment_products;
use App\Models\Treatments;
use App\Models\Products;

class TreatmentProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $treatments = Treatments::where('status', 'completed')->get();
        $products = Products::all();

        $treatmentProducts = [
            // Treatment 1: Facial Treatment - used cleanser and moisturizer
            [
                'treatment_id' => $treatments[0]->id,
                'product_id' => $products[0]->id, // Facial Cleanser
                'quantity_used' => 1,
                'unit_price' => $products[0]->price,
            ],
            [
                'treatment_id' => $treatments[0]->id,
                'product_id' => $products[2]->id, // Moisturizing Cream
                'quantity_used' => 1,
                'unit_price' => $products[2]->price,
            ],
            // Treatment 2: Chemical Peel - used cleanser and serum
            [
                'treatment_id' => $treatments[1]->id,
                'product_id' => $products[0]->id, // Facial Cleanser
                'quantity_used' => 1,
                'unit_price' => $products[0]->price,
            ],
            [
                'treatment_id' => $treatments[1]->id,
                'product_id' => $products[1]->id, // Anti-Aging Serum
                'quantity_used' => 0.50,
                'unit_price' => $products[1]->price,
            ],
            // Treatment 3: Botox - used serum post-treatment
            [
                'treatment_id' => $treatments[2]->id,
                'product_id' => $products[1]->id, // Anti-Aging Serum
                'quantity_used' => 1,
                'unit_price' => $products[1]->price,
            ],
            // Treatment 5: HydraFacial - used cleanser, scrub, mask, and moisturizer
            [
                'treatment_id' => $treatments[4]->id,
                'product_id' => $products[0]->id, // Facial Cleanser
                'quantity_used' => 1,
                'unit_price' => $products[0]->price,
            ],
            [
                'treatment_id' => $treatments[4]->id,
                'product_id' => $products[3]->id, // Exfoliating Scrub
                'quantity_used' => 1,
                'unit_price' => $products[3]->price,
            ],
            [
                'treatment_id' => $treatments[4]->id,
                'product_id' => $products[4]->id, // Face Mask Set
                'quantity_used' => 1,
                'unit_price' => $products[4]->price,
            ],
            [
                'treatment_id' => $treatments[4]->id,
                'product_id' => $products[2]->id, // Moisturizing Cream
                'quantity_used' => 1,
                'unit_price' => $products[2]->price,
            ],
            // Treatment 6: Dermal Filler - used vitamin C serum post-treatment
            [
                'treatment_id' => $treatments[5]->id,
                'product_id' => $products[5]->id, // Vitamin C Serum
                'quantity_used' => 0.50,
                'unit_price' => $products[5]->price,
            ],
            // Treatment 7: Lip Enhancement - used eye cream for aftercare
            [
                'treatment_id' => $treatments[6]->id,
                'product_id' => $products[6]->id, // Eye Cream
                'quantity_used' => 1,
                'unit_price' => $products[6]->price,
            ],
            // Treatment 9: Microdermabrasion - used cleanser and sunscreen
            [
                'treatment_id' => $treatments[8]->id,
                'product_id' => $products[0]->id, // Facial Cleanser
                'quantity_used' => 1,
                'unit_price' => $products[0]->price,
            ],
            [
                'treatment_id' => $treatments[8]->id,
                'product_id' => $products[7]->id, // Sunscreen SPF 50
                'quantity_used' => 1,
                'unit_price' => $products[7]->price,
            ],
            // Treatment 10: Skin Tightening - used moisturizer and sunscreen
            [
                'treatment_id' => $treatments[9]->id,
                'product_id' => $products[2]->id, // Moisturizing Cream
                'quantity_used' => 1,
                'unit_price' => $products[2]->price,
            ],
            [
                'treatment_id' => $treatments[9]->id,
                'product_id' => $products[7]->id, // Sunscreen SPF 50
                'quantity_used' => 1,
                'unit_price' => $products[7]->price,
            ],
        ];

        foreach ($treatmentProducts as $tp) {
            Treatment_products::create($tp);
        }
    }
}
