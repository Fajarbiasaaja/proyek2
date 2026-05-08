<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TechnicianEarningsController
 * 
 * Manage view penghasilan/earnings untuk technician
 * Hitung dari completed bookings dan paid invoices
 */
class TechnicianEarningsController extends Controller
{
    /**
     * Tampilkan ringkasan penghasilan
     */
    public function index()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Get earnings summary
        $completedBookings = Booking::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->with('invoice')
            ->get();

        // Calculate totals
        $totalEarnings = $completedBookings->sum(function ($booking) {
            return $booking->invoice?->total ?? 0;
        });

        $totalCompleted = $completedBookings->count();

        // Get this month earnings
        $thisMonthEarnings = $completedBookings
            ->filter(function ($booking) {
                return $booking->updated_at->isCurrentMonth();
            })
            ->sum(function ($booking) {
                return $booking->invoice?->total ?? 0;
            });

        // Get stats
        $stats = [
            'total_earnings' => $totalEarnings,
            'average_job_value' => $totalCompleted > 0 ? $totalEarnings / $totalCompleted : 0,
            'this_month_earnings' => $thisMonthEarnings,
            'pending_payment' => Invoice::whereIn('booking_id', 
                Booking::where('technician_id', $technician->id)->pluck('id')
            )
            ->where('status', '!=', 'paid')
            ->sum('total'),
        ];

        // Get recent completed bookings
        $recentBookings = Booking::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->with('service', 'invoice')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('technician.earnings.index', compact('stats', 'recentBookings'));
    }

    /**
     * Tampilkan detail earnings dengan breakdown
     */
    public function details()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Get all completed bookings with details
        $bookings = Booking::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->with('service', 'invoice', 'customer')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total_completed' => Booking::where('technician_id', $technician->id)
                ->where('status', 'completed')
                ->count(),
            'total_earnings' => $bookings->sum(function ($booking) {
                return $booking->invoice?->total ?? 0;
            }),
            'by_service' => $this->earningsByService($technician),
            'by_month' => $this->earningsByMonth($technician),
        ];

        return view('technician.earnings.details', compact('bookings', 'stats'));
    }

    /**
     * Export earnings data to CSV/PDF
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        $bookings = Booking::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->with('service', 'invoice', 'customer')
            ->get();

        // Generate CSV
        $csv = "No,Tanggal,Pelanggan,Layanan,Harga,Status Pembayaran,Tanggal Bayar\n";

        foreach ($bookings as $index => $booking) {
            $csv .= ($index + 1) . ",";
            $csv .= $booking->updated_at->format('Y-m-d') . ",";
            $csv .= $booking->customer->name . ",";
            $csv .= $booking->service->name . ",";
            $csv .= ($booking->invoice?->total ?? 0) . ",";
            $csv .= $booking->invoice?->status ?? '-' . ",";
            $csv .= $booking->invoice?->paid_date?->format('Y-m-d') ?? '-' . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="earnings-' . now()->format('Y-m-d') . '.csv"');
    }

    /**
     * Calculate earnings by service type
     */
    private function earningsByService($technician)
    {
        $bookings = Booking::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->with('service', 'invoice')
            ->get();

        return $bookings->groupBy('service.name')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum(function ($booking) {
                        return $booking->invoice?->total ?? 0;
                    }),
                ];
            });
    }

    /**
     * Calculate earnings by month
     */
    private function earningsByMonth($technician)
    {
        $bookings = Booking::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->with('invoice')
            ->get();

        return $bookings->groupBy(function ($booking) {
            return $booking->updated_at->format('Y-m');
        })->map(function ($group) {
            return $group->sum(function ($booking) {
                return $booking->invoice?->total ?? 0;
            });
        });
    }
}
