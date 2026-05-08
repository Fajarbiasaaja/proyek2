<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TechnicianProfileController
 * 
 * Manage profil technician yang ditampilkan kepada customer
 * Termasuk: nama, foto, spesialisasi, deskripsi, contact info
 */
class TechnicianProfileController extends Controller
{
    /**
     * Tampilkan profil technician
     */
    public function show()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Load related data
        $technician->load('user');

        // Get statistics
        $stats = [
            'total_completed' => $technician->bookings()
                ->where('status', 'completed')
                ->count(),
            'average_rating' => $technician->ratings()
                ->avg('rating') ?? 0,
            'total_reviews' => $technician->ratings()
                ->count(),
        ];

        return view('technician.profile.show', compact('technician', 'stats'));
    }

    /**
     * Tampilkan form edit profil
     */
    public function edit()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        return view('technician.profile.edit', compact('technician'));
    }

    /**
     * Update profil technician
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            abort(404, 'Data teknisi tidak ditemukan');
        }

        // Validate input
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'specialization' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'years_experience' => 'nullable|integer|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($technician->photo) {
                \Storage::disk('public')->delete($technician->photo);
            }

            $path = $request->file('photo')->store('technicians', 'public');
            $validated['photo'] = $path;
        }

        $technician->update($validated);

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $request->input('name')]);
        }

        return redirect()->route('technician.profile.show')
            ->with('success', '✓ Profil berhasil diperbarui');
    }
}
