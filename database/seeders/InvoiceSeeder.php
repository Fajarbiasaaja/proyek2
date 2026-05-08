<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all bookings that don't have invoices yet
        $bookings = Booking::doesntHave('invoice')->get();

        $counter = 1;
        foreach ($bookings as $booking) {
            $subtotal = $booking->service->price;
            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;

            // Determine due_date_offset and status based on booking index
            $offset = $counter;
            
            if ($offset === 1) {
                // Invoice unpaid (recent) - due in 7 days
                $due_date = Carbon::now()->addDays(7);
                $status = 'issued';
            } elseif ($offset === 2) {
                // Invoice overdue - was due 5 days ago
                $due_date = Carbon::now()->subDays(5);
                $status = 'overdue';
            } elseif ($offset === 3) {
                // Invoice upcoming due - due in 2 days
                $due_date = Carbon::now()->addDays(2);
                $status = 'issued';
            } else {
                // Invoice soon due - due in 1 day
                $due_date = Carbon::now()->addDays(1);
                $status = 'issued';
            }

            // Generate invoice number
            $invoiceNumber = 'INV-' . Carbon::now()->format('Ymd') . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);

            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $status,
                'due_date' => $due_date,
                'paid_date' => null,
            ]);

            $counter++;
        }
    }
}
