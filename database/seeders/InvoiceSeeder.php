<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoices;
use App\Models\Treatments;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $treatments = Treatments::where('status', 'completed')->get();

        $invoices = [
            [
                'treatment_id' => $treatments[0]->id,
                'treatment_amount' => 150.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 7.50,
                'total_amount' => 157.50,
                'payment_type' => 'card',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-01',
                'paid_at' => '2025-12-01 10:30:00',
                'notes' => null,
            ],
            [
                'treatment_id' => $treatments[1]->id,
                'treatment_amount' => 200.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 10.00,
                'total_amount' => 210.00,
                'payment_type' => 'cash',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-03',
                'paid_at' => '2025-12-03 14:15:00',
                'notes' => null,
            ],
            [
                'treatment_id' => $treatments[2]->id,
                'treatment_amount' => 350.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 17.50,
                'total_amount' => 367.50,
                'payment_type' => 'card',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-05',
                'paid_at' => '2025-12-05 11:00:00',
                'notes' => 'First time client',
            ],
            [
                'treatment_id' => $treatments[3]->id,
                'treatment_amount' => 225.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 11.25,
                'total_amount' => 236.25,
                'payment_type' => 'tabby',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-07',
                'paid_at' => '2025-12-07 16:45:00',
                'notes' => 'Discount of 25 AED applied',
            ],
            [
                'treatment_id' => $treatments[4]->id,
                'treatment_amount' => 220.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 11.00,
                'total_amount' => 231.00,
                'payment_type' => 'cash',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-10',
                'paid_at' => '2025-12-10 09:30:00',
                'notes' => null,
            ],
            [
                'treatment_id' => $treatments[5]->id,
                'treatment_amount' => 400.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 20.00,
                'total_amount' => 420.00,
                'payment_type' => 'bank_transfer',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-12',
                'paid_at' => '2025-12-13 10:00:00',
                'notes' => 'Bank transfer received next day',
            ],
            [
                'treatment_id' => $treatments[6]->id,
                'treatment_amount' => 270.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 13.50,
                'total_amount' => 283.50,
                'payment_type' => 'card',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-15',
                'paid_at' => '2025-12-15 15:20:00',
                'notes' => 'VIP discount applied',
            ],
            [
                'treatment_id' => $treatments[7]->id,
                'treatment_amount' => 500.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 25.00,
                'total_amount' => 525.00,
                'payment_type' => 'tamara',
                'payment_status' => 'pending',
                'invoice_date' => '2025-12-18',
                'paid_at' => null,
                'notes' => 'Tamara installment plan - 3 months',
            ],
            [
                'treatment_id' => $treatments[8]->id,
                'treatment_amount' => 180.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 9.00,
                'total_amount' => 189.00,
                'payment_type' => 'cash',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-20',
                'paid_at' => '2025-12-20 12:00:00',
                'notes' => null,
            ],
            [
                'treatment_id' => $treatments[9]->id,
                'treatment_amount' => 280.00,
                'vat_percentage' => 5.00,
                'vat_amount' => 14.00,
                'total_amount' => 294.00,
                'payment_type' => 'card',
                'payment_status' => 'paid',
                'invoice_date' => '2025-12-22',
                'paid_at' => '2025-12-22 17:30:00',
                'notes' => null,
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoices::create($invoice);
        }
    }
}
