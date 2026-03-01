<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SystemSettingsSeeder::class,
            ClientSeeder::class,
            ProductSeeder::class,
            TreatmentTypeSeeder::class,
            TreatmentSeeder::class,
            TreatmentProductSeeder::class,
            InvoiceSeeder::class,
            StockAdjustmentSeeder::class,
        ]);
    }
}
