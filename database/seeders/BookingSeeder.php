<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get test data
        $customer = Customer::where('email', 'customer@example.com')->first();
        $technician = Technician::first();
        $service = Service::first();

        if (!$customer || !$technician || !$service) {
            return; // Skip if data doesn't exist
        }

        // Create test bookings
        $bookings = [
            // Booking dengan invoice unpaid (recent)
            [
                'customer_id' => $customer->id,
                'technician_id' => $technician->id,
                'service_id' => $service->id,
                'scheduled_date' => Carbon::now()->addDays(5),
                'status' => 'pending',
                'notes' => 'Pemesanan untuk pengujian invoice unpaid',
            ],
            // Booking dengan invoice overdue
            [
                'customer_id' => $customer->id,
                'technician_id' => $technician->id,
                'service_id' => $service->id,
                'scheduled_date' => Carbon::now()->subDays(20),
                'status' => 'completed',
                'notes' => 'Pemesanan untuk pengujian invoice overdue',
            ],
            // Booking dengan invoice upcoming due (dalam 2 hari)
            [
                'customer_id' => $customer->id,
                'technician_id' => $technician->id,
                'service_id' => $service->id,
                'scheduled_date' => Carbon::now()->subDays(10),
                'status' => 'completed',
                'notes' => 'Pemesanan untuk pengujian invoice upcoming due',
            ],
            // Booking dengan invoice soon due (dalam 1 hari)
            [
                'customer_id' => $customer->id,
                'technician_id' => $technician->id,
                'service_id' => $service->id,
                'scheduled_date' => Carbon::now()->subDays(5),
                'status' => 'completed',
                'notes' => 'Pemesanan untuk pengujian invoice soon due',
            ],
        ];

        foreach ($bookings as $bookingData) {
            Booking::create($bookingData);
        }
    }
}
