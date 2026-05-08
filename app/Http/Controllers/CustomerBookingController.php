<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CustomerBookingController
 * 
 * Controller untuk mengelola booking dari perspektif customer.
 * Customer hanya bisa melihat dan mengelola booking milik mereka sendiri.
 */
class CustomerBookingController extends Controller
{
    /**
     * Tampilkan daftar pemesanan customer
     */
    public function index()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            abort(404, 'Data customer tidak ditemukan');
        }

        // Get all bookings untuk customer ini
        $bookings = Booking::where('customer_id', $customer->id)
            ->with('service', 'technician', 'invoice')
            ->orderBy('scheduled_date', 'desc')
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings', 'customer'));
    }

    /**
     * Tampilkan form buat pemesanan baru
     */
    public function create()
    {
        $services = Service::all();
        $technicians = Technician::where('status', '!=', 'inactive')->get();

        return view('customer.bookings.create', compact('services', 'technicians'));
    }

    /**
     * Simpan pemesanan baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            abort(404, 'Data customer tidak ditemukan');
        }

        // Validasi input
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes' => 'nullable|string|max:500',
        ], [
            'scheduled_date.after' => 'Tanggal jadwal harus lebih dari sekarang',
            'scheduled_date.date_format' => 'Format tanggal tidak valid',
        ]);

        // Get service untuk total_price
        $service = Service::findOrFail($validated['service_id']);

        // Buat booking
        $booking = Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $validated['service_id'],
            'technician_id' => $validated['technician_id'] ?? null,
            'scheduled_date' => $validated['scheduled_date'],
            'total_price' => $service->price,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('customer.bookings.index')
            ->with('success', '✓ Pemesanan berhasil dibuat! Menunggu konfirmasi dari admin.');
    }

    /**
     * Tampilkan detail pemesanan
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Jika customer tidak ditemukan
        if (!$customer) {
            abort(404, 'Data customer tidak ditemukan');
        }

        // Pastikan customer hanya bisa lihat booking mereka sendiri
        if ($booking->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini');
        }

        // Load relationships
        $booking->load('service', 'technician', 'invoice');

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Tampilkan form edit pemesanan
     */
    public function edit(Booking $booking)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Pastikan customer hanya bisa edit booking mereka sendiri
        if (!$customer || $booking->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini');
        }

        // Hanya bisa edit jika status pending
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Pemesanan hanya bisa diedit ketika status masih pending');
        }

        $services = Service::all();
        $technicians = Technician::where('status', '!=', 'inactive')->get();

        return view('customer.bookings.edit', compact('booking', 'services', 'technicians'));
    }

    /**
     * Update pemesanan
     */
    public function update(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Pastikan customer hanya bisa update booking mereka sendiri
        if (!$customer || $booking->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini');
        }

        // Hanya bisa update jika status pending
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Pemesanan hanya bisa diedit ketika status masih pending');
        }

        // Validasi input
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get service untuk total_price
        $service = Service::findOrFail($validated['service_id']);

        // Update booking
        $booking->update([
            'service_id' => $validated['service_id'],
            'technician_id' => $validated['technician_id'] ?? null,
            'scheduled_date' => $validated['scheduled_date'],
            'total_price' => $service->price,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('customer.bookings.index')
            ->with('success', '✓ Pemesanan berhasil diperbarui!');
    }

    /**
     * Hapus pemesanan
     */
    public function destroy(Booking $booking)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Pastikan customer hanya bisa hapus booking mereka sendiri
        if (!$customer || $booking->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini');
        }

        // Hanya bisa hapus jika status pending atau cancelled
        if (!in_array($booking->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Pemesanan hanya bisa dihapus ketika status pending atau sudah dibatalkan');
        }

        $booking->delete();

        return redirect()->route('customer.bookings.index')
            ->with('success', '✓ Pemesanan berhasil dihapus!');
    }

    /**
     * Batalkan pemesanan
     */
    public function cancel(Booking $booking)
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        // Pastikan customer hanya bisa batalkan booking mereka sendiri
        if (!$customer || $booking->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini');
        }

        // Hanya bisa batalkan jika belum completed atau cancelled
        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return back()->with('error', 'Pemesanan tidak bisa dibatalkan');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('customer.bookings.index')
            ->with('success', '✓ Pemesanan berhasil dibatalkan!');
    }
}
