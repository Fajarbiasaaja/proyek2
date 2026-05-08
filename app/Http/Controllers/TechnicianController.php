<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * TechnicianController - CRUD Management untuk Data Teknisi/Staf
 * 
 * Mengelola data tenaga teknis (teknisi AC) yang tersedia di sistem.
 * Teknisi adalah staf/karyawan yang akan handle actual service work.
 * 
 * RESTful Resource Methods:
 * - index: List semua technician dengan pagination (10 per page)
 * - create: Form untuk add technician baru
 * - store: Save technician baru
 * - show: Detail view satu technician beserta booking history
 * - edit: Form untuk edit technician
 * - update: Save perubahan technician
 * - destroy: Soft delete technician
 * 
 * Technician Lifecycle:
 * 1. Admin add teknisi dengan nama, phone, email, spesialisasi, status
 * 2. Teknisi available untuk assignment ke booking
 * 3. Booking dapat di-assign ke teknisi
 * 4. Admin bisa update status (available/busy/inactive) sesuai kondisi
 * 5. Admin bisa delete (soft) jika teknisi resign/tidak aktif
 * 
 * Status Management:
 * - available: Teknisi siap menerima booking baru
 * - busy: Sedang ada pekerjaan, tidak terima booking baru (manual assign dalam booking)
 * - inactive: Tidak aktif (cuti panjang, resign, dll)
 * 
 * Relasi:
 * - hasMany bookings: Satu teknisi bisa handle banyak booking
 * - Referenced oleh Booking::technician_id sebagai belongs-to
 * 
 * Specialization:
 * - Field untuk track keahlian teknisi
 * - Contoh: "AC Repair", "AC Installation", "AC Maintenance", dll
 * - Bisa digunakan untuk smart assignment di booking form
 * 
 * Contact Information:
 * - email: Unique untuk komunikasi resmi dengan teknisi
 * - phone: Nomor kontak untuk scheduling & koordinasi
 * - address: Alamat rumah/base untuk reference lokasi
 * 
 * Authorization:
 * - Semua methods dilindungi middleware 'admin'
 * - Admin saja bisa manage technician database
 * 
 * Show Detail:
 * - Menampilkan semua booking yang di-assign ke teknisi ini
 * - Eager load customer & service untuk view
 * - Pagination 5 per page untuk history
 * 
 * Validasi:
 * - email: unique di technicians table (prevent duplicate email assignment)
 * - phone: max 20 chars untuk berbagai format telepon
 * - status: enum ke 3 opsi (available/busy/inactive)
 */
class TechnicianController extends Controller
{
    /**
     * List semua technician dengan pagination
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Paginate 10 technicians per page
        $technicians = Technician::paginate(10);
        return view('technicians.index', compact('technicians'));
    }

    /**
     * Show form untuk create technician baru
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('technicians.create');
    }

    /**
     * Store technician baru ke database
     * 
     * Validasi:
     * - name: required, max 255 chars (nama lengkap)
     * - phone: required, max 20 chars (nomor kontak)
     * - email: required, email format, unique di table (komunikasi resmi)
     * - address: optional, untuk lokasi reference
     * - specialization: required, max 100 chars (tipe service yang dikuasai)
     * - password: required, min 6 chars (untuk login technician)
     * - status: required, enum(available, busy, inactive)
     * 
     * Flow:
     * 1. Validasi input dari form
     * 2. Check apakah email sudah digunakan di tabel users
     * 3. Create Technician record
     * 4. Create User record dengan role 'technician' untuk authentication
     * 5. Hash password sebelum save ke database
     * 6. Redirect ke list dengan success message
     * 
     * Unique Constraint:
     * - email harus unique di BOTH tables (technicians & users)
     * - Prevent duplicate email atau conflict dengan existing users
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:technicians|unique:users',
            'address' => 'nullable|string',
            'specialization' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'status' => 'required|in:available,busy,inactive',
        ]);

        // Create technician profile
        Technician::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'specialization' => $validated['specialization'],
            'status' => $validated['status'],
        ]);

        // Create user account untuk login
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'technician',
        ]);
        
        return redirect()->route('technicians.index')->with('success', 
            'Teknisi berhasil ditambahkan! ✓ User account telah dibuat. Teknisi dapat login di /login/technician dengan email: ' . $validated['email']);
    }

    /**
     * Show detail satu technician beserta booking history
     * 
     * Menampilkan:
     * - Technician info (name, email, phone, specialization, status)
     * - List bookings yang di-assign ke teknisi ini
     * - Eager load customer & service untuk setiap booking
     * - Pagination 5 per page untuk booking history
     * 
     * @param \App\Models\Technician $technician
     * @return \Illuminate\View\View
     */
    public function show(Technician $technician)
    {
        // Fetch bookings untuk technician ini dengan relations
        $bookings = $technician->bookings()->with('customer', 'service')->paginate(5);
        return view('technicians.show', compact('technician', 'bookings'));
    }

    /**
     * Show form untuk edit technician
     * 
     * Populate form dengan existing technician data
     * 
     * @param \App\Models\Technician $technician
     * @return \Illuminate\View\View
     */
    public function edit(Technician $technician)
    {
        return view('technicians.edit', compact('technician'));
    }

    /**
     * Update technician di database
     * 
     * Validasi sama seperti store() method
     * Update semua technician attributes
     * 
     * Email Unique Validation:
     * - unique:technicians,email,{TECHNICIAN_ID} untuk ignore current record
     * - Mencegah error "email already exists" saat edit
     * - Allows teknisi update field lain tanpa unique error
     * 
     * Status Management:
     * - Update status untuk reflect availability (available/busy/inactive)
     * - Admin manually update status berdasarkan workload/schedules
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Technician $technician
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Technician $technician)
    {
        // Validasi input
        // Email unique validation EXCEPT current record ID
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:technicians,email,' . $technician->id,
            'address' => 'nullable|string',
            'specialization' => 'required|string|max:100',
            'status' => 'required|in:available,busy,inactive',
        ]);

        // Update technician
        $technician->update($validated);
        
        return redirect()->route('technicians.show', $technician)->with('success', 'Teknisi berhasil diperbarui');
    }

    /**
     * Delete (soft delete) technician dari database
     * 
     * Menggunakan soft delete:
     * - Technician tetap ada di database dengan deleted_at timestamp
     * - Query normal tidak ambil deleted technicians
     * - Jika restore: deleted_at di-set NULL
     * 
     * Note: Existing bookings untuk technician ini tetap ada
     * (booking has completed work dan invoice sudah issued)
     * 
     * Use Case:
     * - Technician di-delete saat resign, cuti panjang, atau inactive
     * - Data tetap terekam untuk history/audit purpose
     * 
     * @param \App\Models\Technician $technician
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Technician $technician)
    {
        // Soft delete
        $technician->delete();
        
        return redirect()->route('technicians.index')->with('success', 'Teknisi berhasil dihapus');
    }
}
