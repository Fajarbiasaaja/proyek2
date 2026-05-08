<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Booking;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TechnicianRatingController
 * 
 * Manage ratings & reviews yang diterima technician dari customers
 * Rating diberikan setelah booking selesai (completed)
 */
class TechnicianRatingController extends Controller
{
    /**
     * Tampilkan daftar semua rating/review
     */
    public function index()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Get all ratings for this technician
        $ratings = Rating::where('technician_id', $technician->id)
            ->with('booking.customer', 'booking.service')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'average_rating' => Rating::where('technician_id', $technician->id)->avg('rating') ?? 0,
            'total_ratings' => Rating::where('technician_id', $technician->id)->count(),
            'rating_breakdown' => $this->getRatingBreakdown($technician),
        ];

        return view('technician.ratings.index', compact('ratings', 'stats'));
    }

    /**
     * Tampilkan detail rating untuk satu booking
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician || $booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke rating ini');
        }

        $rating = Rating::where('booking_id', $booking->id)->first();

        if (!$rating) {
            abort(404, 'Rating tidak ditemukan');
        }

        $rating->load('booking.customer', 'booking.service');

        return view('technician.ratings.show', compact('rating', 'booking'));
    }

    /**
     * Get rating breakdown (1-5 stars distribution)
     */
    private function getRatingBreakdown($technician)
    {
        $breakdown = [];

        for ($i = 5; $i >= 1; $i--) {
            $count = Rating::where('technician_id', $technician->id)
                ->where('rating', $i)
                ->count();

            $breakdown[$i . ' Star'] = $count;
        }

        return $breakdown;
    }
}
