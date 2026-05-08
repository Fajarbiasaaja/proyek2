<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Technician;
use Illuminate\Http\Request;

/**
 * BookingController - CRUD Management untuk Booking/Order Servis
 * 
 * Bertanggung jawab untuk mengelola lifecycle booking dari customer.
 * Booking adalah central transaction record yang menghubungkan customer,
 * service yang dipilih, dan technician yang akan mengerjakan.
 * 
 * RESTful Resource Methods:
 * - index: List semua booking dengan pagination (10 per page)
 * - create: Form untuk create booking baru
 * - store: Save booking baru ke database
 * - show: Detail view satu booking
 * - edit: Form untuk edit booking
 * - update: Save perubahan booking
 * - destroy: Soft delete booking
 * 
 * Custom Methods:
 * - cancel: Ubah status menjadi 'cancelled'
 * - markAsCompleted: Ubah status menjadi 'completed' + create invoice
 * - createInvoice (private): Helper untuk generate invoice saat completion
 * 
 * Status Workflow:
 * pending (awal) -> confirmed -> in_progress -> completed (generate invoice)
 *                                           \-> cancelled (stop booking)
 * 
 * Business Logic:
 * - Saat create: ambil harga dari service & set sebagai total_price
 * - Saat completion: auto-generate invoice jika belum ada
 * - Invoice calculation: subtotal(price) + 10% tax = total to pay
 * - Invoice creation: dengan invoice_number unik + due_date 7 hari
 * 
 * Authorization:
 * - Semua methods dilindungi dengan middleware 'admin'
 * - Admin bisa create/manage semua booking
 * - Ada separate customer view di CustomerDashboardController
 * 
 * Eager Loading Optimization:
 * - Semua queries pakai ->with('customer', 'service', 'technician')
 * - Mencegah N+1 problem saat loop dalam view
 * - Invoice di-load untuk show() untuk detail page
 * 
 * Validasi &  Rules:
 * - scheduled_date harus future date (after:now)
 * - customer_id/service_id harus ada di database (exists rule)
 * - technician_id bisa null (auto-assign later)
 * - status hanya boleh enum values di database
 * 
 * Redirect Behavior:
 * - After create/update/delete: redirect ke index dengan success message
 * - After complete/cancel: redirect ke show page dengan success message
 * - Error validation: back() with validation errors
 */
class BookingController extends Controller
{
    /**
     * List semua booking dengan pagination dan eager relation loading
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua booking, paginate 10 per page
        // ::with() untuk eager load data customer/service/technician (cegah N+1)
        $bookings = Booking::with('customer', 'service', 'technician')->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show form create booking baru
     * 
     * Fetch dropdown options:
     * - Customers: Semua customer dari database
     * - Services: Daftar service yang tersedia untuk di-book
     * - Technicians: Hanya teknisi dengan status 'available'
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch data untuk dropdown di form
        $customers = Customer::all();
        $services = Service::all();
        // Filter hanya available technicians (tidak busy/inactive)
        $technicians = Technician::where('status', 'available')->get();
        
        return view('bookings.create', compact('customers', 'services', 'technicians'));
    }

    /**
     * Store booking baru ke database
     * 
     * Validasi:
     * - customer_id: required, harus exist di customers table
     * - service_id: required, harus exist di services table
     * - technician_id: optional, harus exist di technicians table jika ada
     * - scheduled_date: required, harus future date (tidak bisa past date)
     * - notes: optional textarea untuk catatan khusus
     * - status: required, hanya boleh 'pending' atau 'confirmed'
     * 
     * Logic:
     * 1. Validasi semua input
     * 2. Fetch service object untuk ambil harga standar
     * 3. Set total_price = service price (bisa override manual di form)
     * 4. Create booking record dengan validated data
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_date' => 'required|date|after:now', // Must be future
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed',
        ]);

        // Fetch service untuk ambil harga default
        $service = Service::find($validated['service_id']);
        $validated['total_price'] = $service->price;

        // Create booking
        Booking::create($validated);
        
        return redirect()->route('bookings.index')->with('success', 'Pemesanan berhasil dibuat');
    }

    /**
     * Show detail satu booking dengan all relations
     * 
     * Eager load:
     * - customer: Data customer yang booking
     * - service: Service yang di-book
     * - technician: Teknisi yang assigned
     * - invoice: Invoice jika booking sudah completed
     * 
     * @param \App\Models\Booking $booking
     * @return \Illuminate\View\View
     */
    public function show(Booking $booking)
    {
        // Authorization: Jika customer, hanya bisa lihat own booking
        if (auth()->user()->role === 'customer') {
            $customer = Customer::where('email', auth()->user()->email)->first();
            if (!$customer || $booking->customer_id !== $customer->id) {
                abort(403, 'Anda tidak memiliki akses ke booking ini');
            }
        }
        
        // Load relasi untuk view detail
        $booking->load('customer', 'service', 'technician', 'invoice');
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show form edit booking
     * 
     * Fetch dropdown options:
     * - Customers: Semua customer bisa di-reassign
     * - Services: Semua service bisa di-change
     * - Technicians: Status != 'inactive' (bisa available/busy)
     * 
     * @param \App\Models\Booking $booking
     * @return \Illuminate\View\View
     */
    public function edit(Booking $booking)
    {
        // Fetch data untuk dropdown
        $customers = Customer::all();
        $services = Service::all();
        // Exclude inactive technicians only (allow busy/available untuk change)
        $technicians = Technician::where('status', '!=', 'inactive')->get();
        
        return view('bookings.edit', compact('booking', 'customers', 'services', 'technicians'));
    }

    /**
     * Update booking di database
     * 
     * Logic:
     * 1. Validasi input (similar ke store, plus completion_notes)
     * 2. Check: jika status berubah menjadi 'completed' & belum ada invoice
     *    -> Auto-create invoice sebelum update
     * 3. Update booking record
     * 
     * Cascading: Status='completed' otomatis trigger invoice generation
     * jika belum ada sebelumnya
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Booking $booking)
    {
        // Validasi input, status sekarang bisa lebih banyak (termasuk in_progress)
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_date' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'completion_notes' => 'nullable|string',
        ]);

        // Check: jika status mau di-complete, generate invoice dulu
        // Condition: booking belum completed sebelumnya AND status baru = completed
        //           AND belum ada invoice
        if ($booking->status !== 'completed' && $validated['status'] === 'completed' && !$booking->invoice()->exists()) {
            $this->createInvoice($booking);
        }

        // Update semua field booking
        $booking->update($validated);
        
        return redirect()->route('bookings.show', $booking)->with('success', 'Pemesanan berhasil diperbarui');
    }

    /**
     * Delete (soft delete) booking dari database
     * 
     * Menggunakan soft delete (via SoftDeletes trait):
     * - Data tetap tertinggal di database dengan deleted_at timestamp
     * - Query normal tidak menampilkan deleted records
     * - Bisa restore bila perlu
     * 
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Booking $booking)
    {
        // Soft delete (set deleted_at = now)
        $booking->delete();
        
        return redirect()->route('bookings.index')->with('success', 'Pemesanan berhasil dihapus');
    }

    /**
     * Cancel booking (ubah status ke 'cancelled')
     * 
     * Ini adalah custom action, bukan RESTful destroy.
     * Alasan: Cancel = Soft state change, tidak delete data
     * Data tetap ada dalam database untuk record/history
     * 
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Booking $booking)
    {
        // Hanya ubah status, jangan delete data
        $booking->update(['status' => 'cancelled']);
        
        return redirect()->route('bookings.show', $booking)->with('success', 'Pemesanan berhasil dibatalkan');
    }

    /**
     * Mark booking sebagai completed dan auto-generate invoice
     * 
     * Workflow:
     * 1. Check: jika belum ada invoice -> createInvoice
     * 2. Update booking status = 'completed'
     * 3. Invoice sekarang ready untuk payment tracking
     * 
     * Invoice Timing: Invoice created saat booking completion, bukan saat booking creation
     * Alasan: Invoice hanya dibuat untuk completed work (faktual billing)
     * 
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsCompleted(Booking $booking)
    {
        // Create invoice jika belum ada
        if (!$booking->invoice()->exists()) {
            $this->createInvoice($booking);
        }
        
        // Update status
        $booking->update(['status' => 'completed']);
        
        return redirect()->route('bookings.show', $booking)->with('success', 'Pemesanan berhasil diselesaikan');
    }

    /**
     * PRIVATE HELPER: Auto-generate invoice untuk completed booking
     * 
     * Invoice Details:
     * - invoice_number: "INV-{TIMESTAMP}-{BOOKING_ID}" untuk unique identifier
     * - subtotal: Total harga dari booking (service price)
     * - tax: 10% dari subtotal (PPN standard Indonesia)
     * - total: subtotal + tax (jumlah yang harus dibayar customer)
     * - status: "issued" (invoice siap dikirim ke customer untuk pembayaran)
     * - due_date: now() + 7 hari (limit waktu pembayaran)
     * 
     * @param \App\Models\Booking $booking
     * @return void
     */
    private function createInvoice(Booking $booking)
    {
        // Generate unique invoice number
        // Format: INV-{YYYYMMDDHHMMSS}-{BOOKING_ID}
        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . $booking->id;
        
        // Calculate amounts
        $subtotal = $booking->total_price;
        $tax = $subtotal * 0.1; // 10% PPN per peraturan Indonesia
        $total = $subtotal + $tax;

        // Create invoice record
        $booking->invoice()->create([
            'invoice_number' => $invoiceNumber,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'issued', // Siap untuk pembayaran
            'due_date' => now()->addDays(7), // Payment deadline
        ]);
    }
}
