<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Slider;
use App\Models\Technician;

/**
 * DashboardController
 * 
 * Controller untuk menampilkan admin dashboard dengan overview data.
 * Dashboard menampilkan:
 * - Statistics cards (total customers, bookings, revenue, etc)
 * - Recent bookings
 * - Services list
 * - Technician statistics
 * - Slider carousel dengan images
 * 
 * Route: GET /dashboard
 */
class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     * 
     * Mengumpulkan semua data yang diperlukan untuk dashboard dan
     * menampilkannya ke view dengan formatting data.
     * 
     * Data yang dikumpulkan:
     * - totalCustomers: Jumlah total customer
     * - totalBookings: Jumlah total booking
     * - completedBookings: Jumlah booking dengan status completed
     * - pendingBookings: Jumlah booking dengan status pending
     * - totalRevenue: Total revenue dari invoice yang sudah dibayar
     * - availableTechnicians: Jumlah technician dengan status available
     * - recentBookings: 5 booking terbaru dengan relationship data
     * - servicesData: Semua service yang tersedia
     * - technicianStats: Semua technician dengan count bookings
     * - sliders: Semua slider yang aktif untuk carousel
     * 
     * @return \Illuminate\View\View Dashboard view dengan data
     */
    public function index()
    {
        // ===== STATISTICS =====
        // Query data untuk statistics cards

        $totalCustomers = Customer::count(); // Total customers
        $totalBookings = Booking::count(); // Total bookings
        $completedBookings = Booking::where('status', 'completed')->count(); // Completed bookings
        $pendingBookings = Booking::where('status', 'pending')->count(); // Pending bookings
        $totalRevenue = Invoice::where('status', 'paid')->sum('total'); // Total revenue dari paid invoices
        $availableTechnicians = Technician::where('status', 'available')->count(); // Available technicians

        // ===== RECENT DATA =====
        // Query data untuk recent items dengan eager loading relationships

        $recentBookings = Booking::latest() // Get latest bookings
            ->take(5) // Ambil 5 terakhir
            ->with('customer', 'service', 'technician') // Eager load relationships untuk performa
            ->get();

        // ===== CATALOG DATA =====
        // Query data untuk list/catalog

        $servicesData = Service::all(); // Semua services
        $technicianStats = Technician::withCount('bookings')->get(); // Technicians dengan count bookings
        
        // ===== SLIDER DATA =====
        // Get active sliders untuk carousel di dashboard
        $sliders = Slider::getActive();

        // ===== RETURN VIEW =====
        // Pass semua data ke view
        return view('dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalBookings' => $totalBookings,
            'completedBookings' => $completedBookings,
            'pendingBookings' => $pendingBookings,
            'totalRevenue' => $totalRevenue,
            'availableTechnicians' => $availableTechnicians,
            'recentBookings' => $recentBookings,
            'servicesData' => $servicesData,
            'technicianStats' => $technicianStats,
            'sliders' => $sliders,
        ]);
    }
}
