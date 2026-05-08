<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * TechnicianDashboardController - Technician-Facing Dashboard View
 * 
 * Provides personalized dashboard untuk technician users.
 * Displays technician-specific data seperti assigned bookings, ratings, work statistics.
 * 
 * Resource Method:
 * - index(): Show technician dashboard dengan statistics & work history
 * 
 * Authorization:
 * - Protected dengan middleware 'technician' (auth + technician role)
 * - Technician hanya lihat own assigned bookings
 * - Technician cannot see other technicians' data
 * 
 * Booking Status Context untuk Technician:
 * - 'pending': Booking baru (belum dikerjakan)
 * - 'in_progress': Sedang dikerjakan
 * - 'completed': Selesai
 * - 'cancelled': Dibatalkan
 * 
 * Data Displayed:
 * 1. Statistics (Summary Cards):
 *    - total_bookings: Total booking count assigned ke technician
 *    - active_bookings: Bookings dengan status='pending' atau 'in_progress'
 *    - completed_bookings: Bookings dengan status='completed'
 *    - average_rating: Rating rata-rata dari completed bookings
 * 
 * 2. Active Bookings (Status pending/in_progress):
 *    - List booking yang sedang berjalan dengan paginate
 *    - Eager load customer, service untuk detail info
 *    - Sorting: orderBy('scheduled_date', 'asc') - yang paling dekat duluan
 *    - For: Quick view pekerjaan yang harus dikerjakan hari ini/segera
 * 
 * 3. Upcoming Bookings (Status pending, scheduled di masa depan):
 *    - List booking yang sudah di-schedule tapi belum dimulai
 *    - Filter: where('scheduled_date', '>', now())
 *    - For: Planning mingguan/bulanan
 * 
 * 4. Completed Bookings (Status completed):
 *    - List booking yang sudah selesai dengan paginate
 *    - Show rating dari customer
 *    - For: Work portfolio/history tracking
 * 
 * Data Retrieval Strategy:
 * 1. Get current authenticated user
 * 2. Query bookings dimana technician_id = user's technician_id
 *    - Gunakan eager loading untuk relationships
 *    - Filter berdasarkan status sesuai kebutuhan
 * 
 * Performance Optimization:
 * - Eager load relationships (customer, service, booking)
 *    Prevents N+1 queries dalam loop
 * - Pagination untuk active & completed bookings
 *    Handle large dataset
 * - Use withCount() untuk rating calculations
 *    Efficient counting tanpa load semua records
 * 
 * Use Case:
 * - Technician login -> redirect to /technician/dashboard
 * - View assigned bookings untuk hari ini/minggu ini
 * - See contact info pelanggan & lokasi servis
 * - Track completed work & customer ratings
 * - Manage schedule dan update booking status
 * 
 * Real-World Workflow:
 * 1. Technician login via email+password
 * 2. AuthController::loginTechnician() redirect
 * 3. Technician see active bookings que untuk hari ini
 * 4. Technician click booking -> Update status (in_progress -> completed)
 * 5. Customer rate technician -> Rating visible di completed bookings
 */
class TechnicianDashboardController extends Controller
{
    /**
     * Show technician dashboard dengan statistics & work data
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get currently authenticated user
        $user = Auth::user();

        // ===== FETCH TECHNICIAN'S BOOKINGS =====
        
        // Get all bookings assigned to this technician
        // Relationship: User -> Booking (via user_id where user.role='technician')
        $allBookings = Booking::where('technician_id', $user->id)
            ->with('customer', 'service')
            ->get();

        // ===== STATISTICS =====
        
        // Total booking count
        $totalBookings = $allBookings->count();
        
        // Active bookings: status='pending' atau 'in_progress'
        $activeBookings = $allBookings
            ->whereIn('status', ['pending', 'in_progress'])
            ->sortBy('scheduled_date')
            ->take(6)
            ->values();
        
        $activeBookingsCount = $allBookings
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        
        // Completed bookings: status='completed'
        $completedBookingsCount = $allBookings
            ->where('status', 'completed')
            ->count();
        
        // Calculate average rating dari completed bookings
        $completedBookingsWithRating = $allBookings
            ->where('status', 'completed')
            ->filter(function($booking) {
                return $booking->rating !== null;
            });
        
        $averageRating = $completedBookingsWithRating->count() > 0
            ? $completedBookingsWithRating->avg('rating')
            : 0;

        // ===== UPCOMING BOOKINGS =====
        
        // Bookings yang dijadwalkan untuk masa depan (belum dimulai)
        $upcomingBookings = $allBookings
            ->where('status', 'pending')
            ->filter(function($booking) {
                return $booking->scheduled_date > now();
            })
            ->sortBy('scheduled_date')
            ->take(6)
            ->values();

        // ===== COMPLETED BOOKINGS WITH PAGINATION =====
        
        // Get completed bookings dengan pagination untuk history viewing
        $completedBookings = Booking::where('technician_id', $user->id)
            ->where('status', 'completed')
            ->with('customer', 'service')
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        // ===== RETURN VIEW =====
        
        // Pass all data to technician dashboard view
        return view('technician.dashboard', [
            'stats' => [
                'total_bookings' => $totalBookings,
                'active_bookings' => $activeBookingsCount,
                'completed_bookings' => $completedBookingsCount,
                'average_rating' => $averageRating,
            ],
            'activeBookings' => $activeBookings,
            'upcomingBookings' => $upcomingBookings,
            'completedBookings' => $completedBookings,
            'user' => $user,
        ]);
    }
}
