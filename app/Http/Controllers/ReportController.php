<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Rating;
use App\Models\Service;
use App\Models\Technician;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

/**
 * ReportController
 * 
 * Controller untuk menampilkan laporan dan analytics untuk admin.
 * Mencakup:
 * - Revenue report (pendapatan)
 * - Booking statistics (statistik pemesanan)
 * - Customer metrics (metrik pelanggan)
 * - Technician performance (performa teknisi)
 * - Payment tracking (pelacakan pembayaran)
 */
class ReportController extends Controller
{
    /**
     * Dashboard dengan overview semua metrics
     * GET /reports/dashboard
     */
    public function dashboard(Request $request)
    {
        $period = $request->query('period', 'monthly'); // daily, weekly, monthly, yearly
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        // Overview Metrics
        $totalBookings = Booking::count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $totalCustomers = Customer::count();
        $totalTechnicians = Technician::count();

        // Recent Bookings
        $recentBookings = Booking::with(['customer', 'technician'])
            ->latest()
            ->limit(5)
            ->get();

        // Revenue Trend (last 7 days)
        $revenueTrend = $this->getRevenueTrend($period, $month, $year);

        // Booking Status Distribution
        $bookingDistribution = $this->getBookingDistribution();

        // Customer Acquisition Trend
        $customerTrend = $this->getCustomerTrend($period);

        return view('admin.reports.dashboard', [
            'overview' => [
                'total_bookings' => $totalBookings,
                'total_revenue' => round($totalRevenue, 2),
                'total_customers' => $totalCustomers,
                'total_technicians' => $totalTechnicians,
            ],
            'recent_bookings' => $recentBookings,
            'revenue_trend' => $revenueTrend,
            'booking_distribution' => $bookingDistribution,
            'customer_trend' => $customerTrend,
        ]);
    }

    /**
     * Revenue Report dengan detail per booking/invoice
     * GET /reports/revenue
     */
    public function revenue(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::createFromFormat('Y-m-d', $request->start_date) : now()->subMonth();
        $endDate = $request->query('end_date') ? Carbon::createFromFormat('Y-m-d', $request->end_date) : now();

        $invoices = Invoice::with(['booking'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalRevenue = $invoices->sum('total');
        $paidRevenue = $invoices->where('status', 'paid')->sum('total');
        $pendingRevenue = $invoices->where('status', 'issued')->sum('total');

        // Revenue by service
        $revenueByService = Booking::selectRaw('services.id, services.name, COUNT(bookings.id) as total_bookings, SUM(bookings.total_price) as revenue')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('services.id', 'services.name')
            ->get();

        // Payment status breakdown
        $paymentStatus = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->get();

        return view('admin.reports.revenue', [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'paid_revenue' => round($paidRevenue, 2),
                'pending_revenue' => round($pendingRevenue, 2),
            ],
            'invoices' => $invoices,
            'revenue_by_service' => $revenueByService,
            'payment_status' => $paymentStatus,
        ]);
    }

    /**
     * Booking Statistics Report
     * GET /reports/bookings
     */
    public function bookings(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::createFromFormat('Y-m-d', $request->start_date) : now()->subMonth();
        $endDate = $request->query('end_date') ? Carbon::createFromFormat('Y-m-d', $request->end_date) : now();

        // Total statistics
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedBookings = Booking::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->whereBetween('created_at', [$startDate, $endDate])->count();
        $pendingBookings = Booking::whereIn('status', ['pending', 'confirmed', 'in_progress'])->whereBetween('created_at', [$startDate, $endDate])->count();

        $completionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 2) : 0;
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 2) : 0;

        // Bookings by service
        $bookingsByService = Booking::selectRaw('services.id, services.name, COUNT(bookings.id) as count')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('services.id', 'services.name')
            ->get();

        // Trend (last 7 days)
        $dailyBookings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Booking::whereDate('created_at', $date->format('Y-m-d'))->count();
            $dailyBookings[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
            ];
        }

        return view('admin.reports.bookings', [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_bookings' => $totalBookings,
                'completed' => $completedBookings,
                'pending' => $pendingBookings,
                'cancelled' => $cancelledBookings,
                'completion_rate' => $completionRate . '%',
                'cancellation_rate' => $cancellationRate . '%',
            ],
            'by_service' => $bookingsByService,
            'daily_trend' => $dailyBookings,
        ]);
    }

    /**
     * Technician Performance Report
     * GET /reports/technicians
     */
    public function technicians(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::createFromFormat('Y-m-d', $request->start_date) : now()->subMonth();
        $endDate = $request->query('end_date') ? Carbon::createFromFormat('Y-m-d', $request->end_date) : now();

        $technicians = Technician::selectRaw('
            technicians.id,
            technicians.name,
            COUNT(bookings.id) as total_jobs,
            SUM(CASE WHEN bookings.status = "completed" THEN 1 ELSE 0 END) as completed_jobs,
            AVG(ratings.rating) as avg_rating,
            COUNT(ratings.id) as total_ratings
        ')
            ->leftJoin('bookings', function ($join) use ($startDate, $endDate) {
                $join->on('technicians.id', '=', 'bookings.technician_id')
                     ->whereBetween('bookings.created_at', [$startDate, $endDate]);
            })
            ->leftJoin('ratings', 'technicians.id', '=', 'ratings.technician_id')
            ->groupBy('technicians.id', 'technicians.name')
            ->orderByDesc('completed_jobs')
            ->get();

        // Map to readable format
        $technicianData = $technicians->map(function ($tech) {
            return [
                'id' => $tech->id,
                'name' => $tech->name,
                'total_jobs' => $tech->total_jobs ?? 0,
                'completed_jobs' => $tech->completed_jobs ?? 0,
                'avg_rating' => round($tech->avg_rating ?? 0, 2),
                'total_ratings' => $tech->total_ratings ?? 0,
                'completion_rate' => $tech->total_jobs > 0 ? round((($tech->completed_jobs ?? 0) / $tech->total_jobs) * 100, 2) : 0,
            ];
        })->sortByDesc('completion_rate')->values();

        return view('admin.reports.technicians', [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'technicians' => $technicianData,
        ]);
    }

    /**
     * Customer Metrics Report
     * GET /reports/customers
     */
    public function customers(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::createFromFormat('Y-m-d', $request->start_date) : now()->subMonth();
        $endDate = $request->query('end_date') ? Carbon::createFromFormat('Y-m-d', $request->end_date) : now();

        $totalCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();

        // Customer with most bookings
        $topCustomers = Customer::selectRaw('customers.id, customers.name, COUNT(bookings.id) as total_bookings, SUM(bookings.total_price) as total_spent')
            ->leftJoin('bookings', 'customers.id', '=', 'bookings.customer_id')
            ->whereBetween('customers.created_at', [$startDate, $endDate])
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_bookings')
            ->limit(10)
            ->get();

        // Retention metrics (customers with multiple bookings)
        $returningCustomers = Customer::selectRaw('COUNT(DISTINCT customer_id) as count')
            ->join('bookings', 'customers.id', '=', 'bookings.customer_id')
            ->selectRaw('COUNT(bookings.id) as booking_count')
            ->groupBy('customer_id')
            ->havingRaw('booking_count > 1')
            ->count();

        return view('admin.reports.customers', [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'new_customers' => $totalCustomers,
                'returning_customers' => $returningCustomers,
            ],
            'top_customers' => $topCustomers,
        ]);
    }

    /**
     * Payment Report dengan detail tracking
     * GET /reports/payments
     */
    public function payments(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::createFromFormat('Y-m-d', $request->start_date) : now()->subMonth();
        $endDate = $request->query('end_date') ? Carbon::createFromFormat('Y-m-d', $request->end_date) : now();

        $payments = Payment::with(['invoice'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalPayments = $payments->count();
        $approvedPayments = $payments->where('status', 'approved')->count();
        $pendingPayments = $payments->where('status', 'pending')->count();
        $rejectedPayments = $payments->where('status', 'rejected')->count();

        $totalAmount = $payments->where('status', 'approved')->sum('amount');

        // Payment by method
        $paymentMethods = $payments->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_amount' => $group->where('status', 'approved')->sum('amount'),
                ];
            })->toArray();

        return view('admin.reports.payments', [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_payments' => $totalPayments,
                'approved_payments' => $approvedPayments,
                'pending_payments' => $pendingPayments,
                'rejected_payments' => $rejectedPayments,
                'total_amount' => round($totalAmount, 2),
            ],
            'by_method' => $paymentMethods,
        ]);
    }

    // ===== HELPER METHODS =====

    /**
     * Get revenue trend
     */
    private function getRevenueTrend($period, $month, $year)
    {
        $data = [];

        if ($period === 'daily') {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $revenue = Invoice::whereDate('created_at', $date->format('Y-m-d'))
                    ->where('status', 'paid')
                    ->sum('total');
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'revenue' => round($revenue, 2),
                ];
            }
        } elseif ($period === 'monthly') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $revenue = Invoice::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'paid')
                    ->sum('total');
                $data[] = [
                    'month' => $date->format('Y-m'),
                    'revenue' => round($revenue, 2),
                ];
            }
        }

        return $data;
    }

    /**
     * Get booking status distribution
     */
    private function getBookingDistribution()
    {
        return [
            'completed' => Booking::where('status', 'completed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'in_progress' => Booking::where('status', 'in_progress')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Get customer acquisition trend
     */
    private function getCustomerTrend($period)
    {
        $data = [];

        if ($period === 'daily') {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $count = Customer::whereDate('created_at', $date->format('Y-m-d'))->count();
                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'new_customers' => $count,
                ];
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $count = Customer::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $data[] = [
                    'month' => $date->format('Y-m'),
                    'new_customers' => $count,
                ];
            }
        }

        return $data;
    }
}
