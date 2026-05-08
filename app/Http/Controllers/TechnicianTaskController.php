<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TechnicianTaskController
 * 
 * Manage tasks/bookings yang ditugaskan kepada technician
 * Task adalah booking yang sudah dikonfirmasi dan siap dikerjakan
 */
class TechnicianTaskController extends Controller
{
    /**
     * Tampilkan daftar task/tugas untuk technician
     */
    public function index()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Get bookings assigned to this technician, sorted by scheduled date
        $tasks = Booking::where('technician_id', $technician->id)
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->with('customer', 'service')
            ->orderBy('scheduled_date', 'asc')
            ->paginate(15);

        // Get statistics
        $stats = [
            'pending_tasks' => Booking::where('technician_id', $technician->id)
                ->where('status', 'confirmed')
                ->count(),
            'in_progress_tasks' => Booking::where('technician_id', $technician->id)
                ->where('status', 'in_progress')
                ->count(),
            'completed_today' => Booking::where('technician_id', $technician->id)
                ->where('status', 'completed')
                ->whereDate('updated_at', now())
                ->count(),
        ];

        return view('technician.tasks.index', compact('tasks', 'stats'));
    }

    /**
     * Tampilkan detail task
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician || $booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke task ini');
        }

        $booking->load('customer', 'service', 'invoice');

        return view('technician.tasks.show', compact('booking'));
    }

    /**
     * Start working on task (ubah status to in_progress)
     */
    public function start(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician || $booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke task ini');
        }

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Task hanya bisa distart dari status confirmed');
        }

        $booking->update(['status' => 'in_progress']);

        return back()->with('success', '✓ Task berhasil dimulai');
    }

    /**
     * Complete task (ubah status ke completed dan generate invoice)
     */
    public function complete(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician || $booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke task ini');
        }

        if ($booking->status !== 'in_progress') {
            return back()->with('error', 'Task hanya bisa diselesaikan dari status in_progress');
        }

        // Validate notes
        $validated = $request->validate([
            'completion_notes' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'completed',
            'completion_notes' => $validated['completion_notes'],
        ]);

        return back()->with('success', '✓ Task berhasil diselesaikan');
    }

    /**
     * Update task information
     */
    public function update(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician || $booking->technician_id !== $technician->id) {
            abort(403, 'Anda tidak memiliki akses ke task ini');
        }

        // Only allow update notes if task is in progress
        if ($booking->status === 'in_progress') {
            $validated = $request->validate([
                'notes' => 'nullable|string|max:500',
            ]);

            $booking->update(['notes' => $validated['notes']]);

            return back()->with('success', '✓ Catatan task berhasil diperbarui');
        }

        return back()->with('error', 'Hanya catatan untuk task in_progress yang bisa diperbarui');
    }
}
