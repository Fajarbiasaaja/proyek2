<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Booking;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Display ratings untuk satu booking (GET /bookings/{id}/ratings)
     */
    public function showForBooking(Booking $booking)
    {
        $rating = Rating::where('booking_id', $booking->id)->first();
        return response()->json(['rating' => $rating]);
    }

    /**
     * Create/Store rating untuk booking yang sudah completed
     * POST /bookings/{id}/ratings
     */
    public function store(Request $request, Booking $booking)
    {
        // Validasi: hanya customer dari booking yang bisa beri rating
        if (Auth::user()->id !== $booking->customer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validasi: booking harus sudah completed
        if ($booking->status !== 'completed') {
            return response()->json(['error' => 'Booking belum selesai'], 422);
        }

        // Validasi input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        // Cek apakah sudah ada rating untuk booking ini
        $existingRating = Rating::where('booking_id', $booking->id)->first();
        if ($existingRating) {
            return response()->json(['error' => 'Rating sudah ada untuk booking ini'], 422);
        }

        // Create rating
        $rating = Rating::create([
            'booking_id' => $booking->id,
            'customer_id' => Auth::user()->id,
            'technician_id' => $booking->technician_id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        return response()->json([
            'message' => 'Rating berhasil disimpan',
            'rating' => $rating
        ], 201);
    }

    /**
     * Update rating
     * PUT /ratings/{id}
     */
    public function update(Request $request, Rating $rating)
    {
        // Validasi: hanya pemberi rating yang bisa edit
        if (Auth::user()->id !== $rating->customer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $rating->update($validated);

        return response()->json([
            'message' => 'Rating berhasil diubah',
            'rating' => $rating
        ]);
    }

    /**
     * Delete rating
     * DELETE /ratings/{id}
     */
    public function destroy(Rating $rating)
    {
        // Validasi: hanya pemberi rating yang bisa delete
        if (Auth::user()->id !== $rating->customer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rating->delete();

        return response()->json(['message' => 'Rating berhasil dihapus']);
    }

    /**
     * Get ratings untuk satu technician
     * GET /technicians/{id}/ratings
     */
    public function getTechnicianRatings(Technician $technician)
    {
        $ratings = Rating::where('technician_id', $technician->id)
            ->with(['customer', 'booking'])
            ->latest()
            ->paginate(10);

        $averageRating = Rating::averageForTechnician($technician->id);
        $totalRatings = Rating::countForTechnician($technician->id);

        return response()->json([
            'technician' => $technician,
            'ratings' => $ratings,
            'statistics' => [
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
            ]
        ]);
    }

    /**
     * Get top rated technicians
     * GET /technicians/top-rated
     */
    public function getTopRatedTechnicians()
    {
        $topTechnicians = Technician::query()
            ->selectRaw('technicians.*, AVG(ratings.rating) as avg_rating, COUNT(ratings.id) as total_ratings')
            ->leftJoin('ratings', 'technicians.id', '=', 'ratings.technician_id')
            ->where('technicians.is_active', true)
            ->groupBy('technicians.id')
            ->orderByDesc('avg_rating')
            ->limit(10)
            ->get();

        return response()->json(['technicians' => $topTechnicians]);
    }
}
