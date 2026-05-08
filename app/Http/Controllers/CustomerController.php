<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

/**
 * CustomerController - CRUD Management untuk Data Pelanggan/Customer
 * 
 * Mengelola customer database yang merepresentasikan clients/pelanggan.
 * Customer adalah profile yang terpisah dari User authentication.
 * 
 * Customer vs User:
 * - User: Authentication record (email, password, OAuth info)
 *   Bisa admin atau customer role
 * - Customer: Profile data dengan address, contact, city, postal_code
 *   Hanya untuk customer role, not for admin
 * 
 * Relasi:
 * - belongsTo user (via email - implicit relationship)
 * - hasMany bookings: Satu customer bisa booking berkali-kali
 * - hasManyThrough invoices: Akses invoices via booking
 * 
 * Use Cases:
 * - Admin manage customer database (add/edit/delete)
 * - Customer self-edit profile via ProfileController
 * - Booking system menggunakan customer_id untuk track who booked
 * 
 * RESTful Resource Methods:
 * - index: List semua customer dengan pagination (10 per page)
 * - create: Form untuk add customer baru
 * - store: Save customer baru
 * - show: Detail view satu customer beserta booking history
 * - edit: Form untuk edit customer profile
 * - update: Save perubahan customer
 * - destroy: Soft delete customer
 * 
 * Customer Data Fields:
 * - name: Nama lengkap pelanggan
 * - email: Email address (unique, untuk komunikasi & booking reference)
 * - phone: Nomor telepon untuk kontak direct
 * - address: Alamat lengkap (untuk lokasi service)
 * - city: Kota/kotamadya (optional, untuk area segmentation)
 * - postal_code: Kode pos (optional, untuk logistics)
 * 
 * Authorization:
 * - Semua methods dilindungi middleware 'admin'
 * - Admin saja bisa manage customer database
 * - Customer edit own profile via ProfileController (tidak via sini)
 * 
 * Validasi:
 * - email: unique di customers table (prevent duplicate email)
 * - phone: max 20 chars (berbagai format international)
 * - name & address: required untuk contact information
 * 
 * Show Detail:
 * - Menampilkan customer info lengkap
 * - Booking history pagination 5 per page
 * - Eager load service & technician untuk setiap booking
 * 
 * Soft Delete:
 * - Customer tetap ada di database dengan deleted_at timestamp
 * - Useful untuk historical data (customer lama yang tidak aktif)
 * - Dapat di-restore jika customer comeback
 */
class CustomerController extends Controller
{
    /**
     * List semua customer dengan pagination
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Paginate 10 customers per page
        $customers = Customer::paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show form untuk create customer baru
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store customer baru ke database
     * 
     * Validasi:
     * - name: required, max 255 chars (nama lengkap)
     * - phone: required, max 20 chars (nomor kontak)
     * - email: required, email format, unique ke customers table
     * - address: required (alamat service lokasi)
     * - city: optional, max 100 chars (area designation)
     * - postal_code: optional, max 20 chars (postal code)
     * 
     * Notes:
     * - email unique untuk identify customer
     * - address required karena service datang ke rumah customer
     * - Bisa create customer tanpa User account (manual entry)
     * - atau create saat user register via AuthController
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
            'email' => 'required|email|unique:customers',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Create customer
        Customer::create($validated);
        
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    /**
     * Show detail satu customer beserta booking history
     * 
     * Menampilkan:
     * - Customer info (name, email, phone, address, city, postal_code)
     * - Booking history dengan pagination 5 per page
     * - Eager load service & technician untuk setiap booking
     * - Total bookings for summary
     * 
     * @param \App\Models\Customer $customer
     * @return \Illuminate\View\View
     */
    public function show(Customer $customer)
    {
        // Fetch bookings dengan relations
        $bookings = $customer->bookings()->with('service', 'technician')->paginate(5);
        return view('customers.show', compact('customer', 'bookings'));
    }

    /**
     * Show form untuk edit customer
     * 
     * Populate form dengan existing customer data
     * 
     * @param \App\Models\Customer $customer
     * @return \Illuminate\View\View
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update customer di database
     * 
     * Validasi sama seperti store() method
     * Email unique validation EXCEPT current record ID
     * Format: unique:customers,email,{CUSTOMER_ID}
     * 
     * Update all customer fields dari form
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Customer $customer)
    {
        // Validasi input
        // Email unique EXCEPT current customer record
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Update customer
        $customer->update($validated);
        
        return redirect()->route('customers.show', $customer)->with('success', 'Pelanggan berhasil diperbarui');
    }

    /**
     * Delete (soft delete) customer dari database
     * 
     * Soft delete behavior:
     * - Set deleted_at = now()
     * - Customer tetap ada di database untuk history/audit
     * - Query normal tidak return deleted customer
     * - Booking history tetap ada (customer_id di booking table)
     * 
     * Use Case:
     * - Customer tidak aktif lagi
     * - Customers yang sudah tidak perlu track
     * - Data preservation untuk audit trail
     * 
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Customer $customer)
    {
        // Soft delete
        $customer->delete();
        
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
