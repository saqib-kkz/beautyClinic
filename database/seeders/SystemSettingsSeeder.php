<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\System_settings;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'clinic_name',
                'value' => 'Beauty Clinic',
                'type' => 'string',
                'description' => 'Name of the clinic for invoices and branding'
            ],
            [
                'key' => 'clinic_address',
                'value' => '123 Beauty Street, City, Country',
                'type' => 'string',
                'description' => 'Clinic address for invoices'
            ],
            [
                'key' => 'clinic_phone',
                'value' => '+1-234-567-8900',
                'type' => 'string',
                'description' => 'Clinic contact phone number'
            ],
            [
                'key' => 'clinic_email',
                'value' => 'info@beautyclinic.com',
                'type' => 'string',
                'description' => 'Clinic contact email'
            ],
            [
                'key' => 'default_vat_percentage',
                'value' => '5.00',
                'type' => 'decimal',
                'description' => 'Default VAT percentage for treatments'
            ],
            [
                'key' => 'low_stock_threshold',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Default threshold for low stock alerts'
            ],
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'type' => 'string',
                'description' => 'Currency symbol for invoices'
            ],
            [
                'key' => 'invoice_prefix',
                'value' => 'INV',
                'type' => 'string',
                'description' => 'Prefix for invoice numbers'
            ]
        ];

        foreach ($settings as $setting) {
            System_settings::create($setting);
        }
    }
}
