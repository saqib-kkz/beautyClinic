<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TreatmentType;

class TreatmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $treatmentTypes = [
            [
                'name' => 'Facial Treatment',
                'description' => 'Deep cleansing and rejuvenating facial treatments',
                'price' => 150.00,
            ],
            [
                'name' => 'Chemical Peel',
                'description' => 'Exfoliating treatment to improve skin texture and tone',
                'price' => 200.00,
            ],
            [
                'name' => 'Microdermabrasion',
                'description' => 'Non-invasive skin resurfacing procedure',
                'price' => 180.00,
            ],
            [
                'name' => 'Botox Injection',
                'description' => 'Anti-wrinkle injectable treatment for fine lines',
                'price' => 350.00,
            ],
            [
                'name' => 'Dermal Filler',
                'description' => 'Volume restoration and contouring with hyaluronic acid fillers',
                'price' => 400.00,
            ],
            [
                'name' => 'Laser Hair Removal',
                'description' => 'Permanent hair reduction using laser technology',
                'price' => 250.00,
            ],
            [
                'name' => 'HydraFacial',
                'description' => 'Multi-step treatment combining cleansing, exfoliation, and hydration',
                'price' => 220.00,
            ],
            [
                'name' => 'PRP Therapy',
                'description' => 'Platelet-rich plasma therapy for skin rejuvenation and hair restoration',
                'price' => 500.00,
            ],
            [
                'name' => 'Lip Enhancement',
                'description' => 'Lip augmentation and shaping with dermal fillers',
                'price' => 300.00,
            ],
            [
                'name' => 'Skin Tightening',
                'description' => 'Non-surgical skin tightening using radiofrequency or ultrasound',
                'price' => 280.00,
            ],
        ];

        foreach ($treatmentTypes as $type) {
            TreatmentType::create($type);
        }
    }
}
