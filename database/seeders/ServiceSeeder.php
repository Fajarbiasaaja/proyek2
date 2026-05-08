<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Service AC Split Standar',
                'description' => 'Pembersihan, pengisian freon, dan pengecekan kompresol',
                'price' => 150000,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Service AC Split Premium',
                'description' => 'Service lengkap termasuk pembongkaran dan pembersihan seluruh komponen',
                'price' => 250000,
                'duration_minutes' => 120,
            ],
            [
                'name' => 'Perbaikan AC Rusak',
                'description' => 'Diagnosa dan perbaikan AC yang rusak / tidak berfungsi',
                'price' => 300000,
                'duration_minutes' => 90,
            ],
            [
                'name' => 'Pengisian Freon AC',
                'description' => 'Pengisian freon untuk AC yang sudah bocor',
                'price' => 200000,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Perawatan Rutin AC Window',
                'description' => 'Pembersihan filter dan pengecekan AC Window',
                'price' => 100000,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Instalasi AC Split Baru',
                'description' => 'Instalasi AC Split termasuk pipa dan kabel copper',
                'price' => 500000,
                'duration_minutes' => 180,
            ],
            [
                'name' => 'Uninstal / Pindahkan AC',
                'description' => 'Uninstal AC dan persiapan untuk pindah lokasi',
                'price' => 200000,
                'duration_minutes' => 60,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
