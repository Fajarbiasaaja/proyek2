<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TechnicianBookingController
 * 
 * Controller untuk mengelola booking dari perspektif technician.
 * Technician hanya bisa melihat dan mengelola booking yang ditugaskan ke mereka.
 */
class TechnicianBookingController extends Controller
{
    /**
     * Tampilkan daftar pesanan masuk (bookings assigned to this technician)
     */
    public function index()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Get all bookings assigned to this technician
        $bookings = Booking::where('technician_id', $technician->id)
            ->with('service', 'customer', 'invoice')
            ->orderBy('scheduled_date', 'desc')
            ->paginate(10);

        return view('technician.bookings.index', compact('bookings', 'technician'));
    }

    /**
     * Tampilkan detail pesanan
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Cek apakah booking ini milik technician ini
        if ($booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini');
        }

        $booking->load('service', 'customer', 'invoice', 'ratings');

        return view('technician.bookings.show', compact('booking', 'technician'));
    }

    /**
     * Mark booking as completed
     */
    public function markAsCompleted(Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Cek apakah booking ini milik technician ini
        if ($booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini');
        }

        // Update booking status ke completed
        $booking->update(['status' => 'completed']);

        return redirect()
            ->route('technician.bookings.show', $booking)
            ->with('success', 'Pesanan berhasil ditandai selesai');
    }

    /**
     * Accept a booking (change status from pending to confirmed)
     */
    public function accept(Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Cek apakah booking ini milik technician ini
        if ($booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini');
        }

        // Update booking status ke confirmed
        $booking->update(['status' => 'confirmed']);

        return redirect()
            ->route('technician.bookings.show', $booking)
            ->with('success', 'Pesanan berhasil diterima');
    }

    /**
     * Reject a booking
     */
    public function reject(Booking $booking, Request $request)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Cek apakah booking ini milik technician ini
        if ($booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini');
        }

        // Validate request
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        // Update booking status ke cancelled
        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->reason,
        ]);

        return redirect()
            ->route('technician.bookings.index')
            ->with('success', 'Pesanan berhasil ditolak');
    }
}
