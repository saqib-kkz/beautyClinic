<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'full_name' => 'Emma Wilson',
                'contact_number' => '+1-555-0101',
                'notes' => 'Sensitive skin, prefers organic products'
            ],
            [
                'full_name' => 'Michael Brown',
                'contact_number' => '+1-555-0102',
                'notes' => 'Regular monthly facial appointments'
            ],
            [
                'full_name' => 'Sarah Davis',
                'contact_number' => '+1-555-0103',
                'notes' => 'First-time client, interested in anti-aging treatments'
            ],
            [
                'full_name' => 'James Miller',
                'contact_number' => '+1-555-0104',
                'notes' => null
            ],
            [
                'full_name' => 'Lisa Anderson',
                'contact_number' => '+1-555-0105',
                'notes' => 'Allergic to fragranced products'
            ],
            [
                'full_name' => 'David Taylor',
                'contact_number' => '+1-555-0106',
                'notes' => 'Prefers evening appointments'
            ],
            [
                'full_name' => 'Jennifer White',
                'contact_number' => '+1-555-0107',
                'notes' => 'VIP client - 10% discount on all services'
            ]
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
