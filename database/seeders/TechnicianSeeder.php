<?php

namespace Database\Seeders;

use App\Models\Technician;
use Illuminate\Database\Seeder;

class TechnicianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technicians = [
            [
                'name' => 'Hendra Gunawan',
                'phone' => '081111111111',
                'email' => 'hendra@example.com',
                'address' => 'Jl. Veteran No. 111',
                'specialization' => 'AC Split',
                'status' => 'available',
            ],
            [
                'name' => 'Yanto Hermawan',
                'phone' => '082222222222',
                'email' => 'yanto@example.com',
                'address' => 'Jl. Diponegoro No. 222',
                'specialization' => 'AC Window & Central',
                'status' => 'available',
            ],
            [
                'name' => 'Bambang Sudirjo',
                'phone' => '083333333333',
                'email' => 'bambang@example.com',
                'address' => 'Jl. Kartini No. 333',
                'specialization' => 'AC Split & Maintenance',
                'status' => 'busy',
            ],
            [
                'name' => 'Sutrisno',
                'phone' => '084444444444',
                'email' => 'sutrisno@example.com',
                'address' => 'Jl. Gajah Mada No. 444',
                'specialization' => 'All Type AC',
                'status' => 'available',
            ],
            [
                'name' => 'Wardi Setiawan',
                'phone' => '085555555555',
                'email' => 'wardi@example.com',
                'address' => 'Jl. Hayam Wuruk No. 555',
                'specialization' => 'AC Maintenance',
                'status' => 'available',
            ],
        ];

        foreach ($technicians as $technician) {
            Technician::create($technician);

            // make a matching user account so technician can login
            \App\Models\User::create([
                'name' => $technician['name'],
                'email' => $technician['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'technician',
                'email_verified_at' => now(),
            ]);
        }
    }
}
