<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

/**
 * CustomerDashboardController - Customer-Facing Dashboard View
 * 
 * Provides personalized dashboard untuk customer (non-admin) users.
 * Displays customer-specific data seperti bookings, invoices, expenses.
 * 
 * Resource Method:
 * - index(): Show customer dashboard dengan statistics & history
 * 
 * Authorization:
 * - Protected dengan middleware 'customer' (auth + customer role)
 * - Customer hanya lihat own data (via Auth::user())
 * - Customer cannot see other customers' data
 * 
 * Data Displayed:
 * 1. Statistics (Summary Cards):
 *    - totalBookings: Total booking count
 *    - completedBookings: Bookings dengan status='completed'
 *    - pendingBookings: Bookings dengan status='pending'
 *    - totalExpense: Sum of all paid invoices (total spent)
 * 
 * 2. Recent Bookings (Last 5):
 *    - List booking terbaru dengan eager load service & technician
 *    - Sorting: orderBy('created_at', 'desc') untuk newest first
 *    - For: Track ongoing & recent services
 * 
 * 3. Unpaid Invoices (Last 3):
 *    - List invoice yang belum dibayar (status != 'paid')
 *    - Eager load booking untuk reference
 *    - For: Reminder pembayaran yang pending
 * 
 * Data Retrieval Strategy:
 * 1. Get current authenticated user
 * 2. Find customer via email matching
 *    - Why email? User created via OAuth/Register -> Customer created
 *    - Email is link between User & Customer tables
 * 
 * 2. Query data menggunakan customer.bookings() relationship
 *    - Never query bookings directly (would get all bookings)
 *    - Always filter via customer -> own booking only
 * 
 * 3. Calculate statistics dengan WHERE clauses
 *    - totalBookings: count() all bookings
 *    - completedBookings: where('status', 'completed')->count()
 *    - pendingBookings: where('status', 'pending')->count()
 *    - totalExpense: invoice totals for paid status only
 * 
 * Error Handling:
 * - If customer record not found: abort(404)
 *    Reason: Indicates data inconsistency (user exist but customer missing)
 *    Shouldn't happen in normal flow (SocialAuthController creates customer)
 * 
 * Performance Optimization:
 * - Eager load relationships (service, technician, booking)
 *    Prevents N+1 queries dalam loop
 * - Limit results (take(5), take(3))
 *    Reduces data transfer & query performance
 * - Pagination handled via ->get() instead of ->paginate()
 *    Because: Limited result set, no pagination needed
 * 
 * Use Case:
 * - Customer login -> redirect to /customer-dashboard
 * - View personal statistics & history
 * - See pending invoices needing payment
 * - Track recent service history
 * 
 * Real-World Workflow:
 * 1. Customer login via OAuth/Email+Password
 * 2. DashboardController (admin only) atau SocialAuthController redirect
 * 3. Customer see own bookings count, total spent, pending fees
 * 4. Customer click Recent Booking -> Go to booking detail page
 * 5. Customer see Unpaid Invoices -> Pay or view details
 */
class CustomerDashboardController extends Controller
{
    /**
     * Show customer dashboard dengan statistics & personal data
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get currently authenticated user
        $user = Auth::user();
        
        // Find customer record by matching email
        // Email is the connection between User & Customer
        $customer = Customer::where('email', $user->email)->first();

        // Error handling: customer harus exist
        // Shouldn't happen unless data inconsistency
        if (!$customer) {
            abort(404, 'Customer data not found');
        }

        // ===== STATISTICS =====
        
        // Total booking count
        $totalBookings = $customer->bookings()->count();
        
        // Booking dengan status 'completed' (finished services)
        $completedBookings = $customer->bookings()
            ->where('status', 'completed')
            ->count();
        
        // Booking dengan status 'pending' (waiting confirmation)
        $pendingBookings = $customer->bookings()
            ->where('status', 'pending')
            ->count();
        
        // Total expense: Sum of all PAID invoices
        // Query strategy:
        // 1. Get customer's booking IDs
        // 2. Find invoices via these booking IDs
        // 3. Filter status='paid' only (ignore draft/overdue)
        // 4. Sum total amount
        $totalExpense = Invoice::whereIn('booking_id', $customer->bookings()->pluck('id'))
            ->where('status', 'paid')
            ->sum('total');

        // ===== RECENT BOOKINGS =====
        
        // Get 5 most recent bookings dengan relationship data
        $recentBookings = $customer->bookings()
            ->with('service', 'technician')  // Eager load to prevent N+1
            ->orderBy('created_at', 'desc')   // Newest first
            ->take(5)                         // Limit 5 results
            ->get();

        // ===== UNPAID INVOICES =====
        
        // Get all unpaid invoices untuk notifikasi
        // Status != 'paid': Include draft, issued, overdue (all not-paid)
        $unpaidInvoices = Invoice::whereIn('booking_id', $customer->bookings()->pluck('id'))
            ->where('status', '!=', 'paid')                // Filter not-paid
            ->with('booking')                              // Eager load for context
            ->orderBy('created_at', 'desc')                // Newest first
            ->get();

        // ===== NOTIFICATION DATA =====
        
        // Hitung invoices yang sudah overdue (terlambat)
        $overdueInvoices = $unpaidInvoices->filter(function($invoice) {
            return $invoice->status === 'overdue' || now()->isAfter($invoice->due_date);
        });

        // Hitung invoices yang akan jatuh tempo (dalam 3 hari ke depan)
        $upcomingDueInvoices = $unpaidInvoices->filter(function($invoice) {
            return $invoice->status !== 'overdue' && 
                   now()->isBefore($invoice->due_date) &&
                   now()->addDays(3)->isAfter($invoice->due_date);
        });

        // Total count notifikasi
        $overdueCount = $overdueInvoices->count();
        $upcomingCount = $upcomingDueInvoices->count();
        $totalNotificationCount = $overdueCount + $upcomingCount;

        // ===== RETURN VIEW =====
        
        // Pass all data to customer dashboard view
        return view('customer.dashboard', [
            'customer' => $customer,
            'totalBookings' => $totalBookings,
            'completedBookings' => $completedBookings,
            'pendingBookings' => $pendingBookings,
            'totalExpense' => $totalExpense,
            'recentBookings' => $recentBookings,
            'unpaidInvoices' => $unpaidInvoices->take(3),  // Take only 3 for sidebar
            'overdueInvoices' => $overdueInvoices,
            'upcomingDueInvoices' => $upcomingDueInvoices,
            'overdueCount' => $overdueCount,
            'upcomingCount' => $upcomingCount,
            'totalNotificationCount' => $totalNotificationCount,
        ]);
    }
}
