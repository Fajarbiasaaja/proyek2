<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'customer@example.com',
                'address' => 'Jl. Merdeka No. 123',
                'city' => 'Jakarta',
                'postal_code' => '12345',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'phone' => '082345678901',
                'email' => 'siti.customer@example.com',
                'address' => 'Jl. Sudirman No. 456',
                'city' => 'Bandung',
                'postal_code' => '40123',
            ],
            [
                'name' => 'Ahmad Wijaya',
                'phone' => '083456789012',
                'email' => 'ahmad@example.com',
                'address' => 'Jl. Gadjah Mada No. 789',
                'city' => 'Surabaya',
                'postal_code' => '60123',
            ],
            [
                'name' => 'Ratna Kartika',
                'phone' => '084567890123',
                'email' => 'ratna@example.com',
                'address' => 'Jl. Ahmad Yani No. 321',
                'city' => 'Medan',
                'postal_code' => '20123',
            ],
            [
                'name' => 'Eka Putra Mulya',
                'phone' => '085678901234',
                'email' => 'eka@example.com',
                'address' => 'Jl. Imam Bonjol No. 654',
                'city' => 'Yogyakarta',
                'postal_code' => '55123',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
