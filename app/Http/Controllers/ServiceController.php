<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

/**
 * ServiceController - CRUD Management untuk Layanan AC yang Ditawarkan
 * 
 * Mengelola katalog service/layanan yang bisa di-booking oleh customer.
 * Setiap service memiliki harga standar dan durasi estimasi.
 * 
 * RESTful Resource Methods:
 * - index: List semua service dengan pagination (10 per page)
 * - create: Form untuk add service baru
 * - store: Save service baru
 * - show: Detail view satu service beserta bookings history
 * - edit: Form untuk edit service
 * - update: Save perubahan service
 * - destroy: Soft delete service
 * 
 * Service Lifecycle:
 * 1. Admin add service dengan nama, description, price, duration
 * 2. Service available untuk customer booking
 * 3. Customer dapat book service ini
 * 4. Booking menggunakan harga dari service sebagai dasar pricing
 * 5. Admin bisa edit/update service kapan saja
 * 6. Admin bisa delete (soft) service jika sudah tidak digunakan
 * 
 * Business Rules:
 * - price: Harga standar service (dalam Rupiah dengan 2 desimal)
 * - duration_minutes: Estimasi durasi pengerjaan untuk scheduling
 * - description: Detail penjelasan apa yang included dalam service
 * - Status aktif vs inactive: Controlled via soft delete
 * 
 * Relasi:
 * - hasMany bookings: Satu service bisa dibooking berkali-kali
 * - Referenced oleh Booking model sebagai belongs-to relationship
 * 
 * Authorization:
 * - Semua methods dilindungi middleware 'admin'
 * - Admin saja bisa manage service catalog
 * 
 * Pricing Strategy:
 * - Price di-set sebagai standar baseline
 * - Saat booking, harga dari service dikopi ke booking->total_price
 * - Bisa di-override per booking jika ada special negotiation
 * - Invoice kemudian digenerate dari booking->total_price
 * 
 * Show Detail:
 * - Menampilkan semua booking yang menggunakan service ini
 * - Eager load customer & technician untuk view
 * - Pagination 5 per page untuk history
 */
class ServiceController extends Controller
{
    /**
     * List semua service dengan pagination
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Paginate 10 services per page dengan count bookings
        $services = Service::withCount('bookings')->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * Show form untuk create service baru
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store service baru ke database
     * 
     * Validasi:
     * - name: required, max 255 chars (nama service display)
     * - description: optional, long text untuk penjelasan detail
     * - price: required, numeric, min 0 (harga dalam Rupiah)
     * - duration_minutes: required, integer, min 15 menit (durasi pengerjaan)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:15',
        ]);

        // Create service
        Service::create($validated);
        
        return redirect()->route('services.index')->with('success', 'Layanan berhasil ditambahkan');
    }

    /**
     * Show detail satu service beserta booking history
     * 
     * Menampilkan:
     * - Service info (name, description, price, duration)
     * - List bookings yang menggunakan service ini
     * - Eager load customer & technician untuk setiap booking
     * - Pagination 5 per page untuk booking history
     * 
     * @param \App\Models\Service $service
     * @return \Illuminate\View\View
     */
    public function show(Service $service)
    {
        // Fetch bookings untuk service ini dengan relations
        $bookings = $service->bookings()->with('customer', 'technician')->paginate(5);
        return view('services.show', compact('service', 'bookings'));
    }

    /**
     * Show form untuk edit service
     * 
     * Populate form dengan existing service data
     * 
     * @param \App\Models\Service $service
     * @return \Illuminate\View\View
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update service di database
     * 
     * Validasi sama seperti store() method
     * Update all service attributes baru
     * 
     * Notes:
     * - Kalau harga diubah, hanya affect booking baru (booking existing tetap)
     * - Durasi bisa di-adjust untuk scheduling estimate
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Service $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Service $service)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:15',
        ]);

        // Update service
        $service->update($validated);
        
        return redirect()->route('services.show', $service)->with('success', 'Layanan berhasil diperbarui');
    }

    /**
     * Delete (soft delete) service dari database
     * 
     * Menggunakan soft delete:
     * - Service tetap ada di database dengan deleted_at timestamp
     * - Query normal tidak ambil deleted services
     * - Jika restore: deleted_at di-set NULL
     * 
     * Note: Existing bookings untuk service ini tetap valid
     * (mereka punya copy harga di booking->total_price)
     * 
     * @param \App\Models\Service $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Service $service)
    {
        // Soft delete
        $service->delete();
        
        return redirect()->route('services.index')->with('success', 'Layanan berhasil dihapus');
    }
}
