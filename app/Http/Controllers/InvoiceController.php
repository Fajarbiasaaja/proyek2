<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * InvoiceController - Management Invoice/Tagihan untuk Pembayaran
 * 
 * Mengelola invoice (tagihan) yang di-create setelah booking completed.
 * Invoice adalah dokumen finansial untuk payment tracking.
 * 
 * Resource Methods (Read-Only + Update):
 * - index: List semua invoice dengan pagination (10 per page)
 * - show: Detail view satu invoice dengan all related data
 * - edit: Form untuk edit invoice status
 * - update: Save perubahan invoice (status, paid_date)
 * - destroy: Soft delete draft invoice (hanya draft saja allowed)
 * 
 * Custom Actions:
 * - markAsPaid: Quick action untuk mark invoice sebagai paid
 * 
 * NO Create/Store:
 * - Invoice tidak dibuat manual via form
 * - Auto-created oleh BookingController saat booking completion (markAsCompleted/store)
 * - Trigger: Saat booking status berubah menjadi 'completed'
 * - Helper: BookingController::createInvoice()
 * 
 * Invoice Lifecycle:
 * 1. Booking created dengan status 'pending'
 * 2. Booking marked 'in_progress' (work started)
 * 3. Booking marked 'completed' (work done)
 *    --> Auto-create Invoice dengan status 'draft'
 * 4. Admin issues invoice (ubah status -> 'issued')
 *    --> Invoice siap untuk dikirim ke customer
 * 5. Customer bayar invoice
 * 6. Admin marks as 'paid' via markAsPaid atau update
 * 7. If belum dibayar after due_date -> overdue
 * 
 * Payment Status:
 * - draft: Invoice baru, belum dikirim (dalam review)
 * - issued: Sudah dikirim ke customer, menunggu pembayaran
 * - paid: Pembayaran sudah diterima (final state)
 * - overdue: Sudah lewat due_date, belum dibayar
 * 
 * Financial Fields:
 * - invoice_number: Unique identifier "INV-{TIMESTAMP}-{ID}"
 * - subtotal: Harga service dari booking
 * - tax: 10% PPN dari subtotal
 * - total: subtotal + tax (yang harus dibayar)
 * - due_date: Deadline pembayaran (default +7 hari dari creation)
 * - paid_date: Timestamp saat pembayaran diterima
 * 
 * Relasi & Eager Loading:
 * - belongsTo booking (one-invoice-per-booking)
 * - Through booking -> customer (who pays)
 * - Through booking -> service (what service was done)
 * - Through booking -> technician (who did the work)
 * 
 * Authorization:
 * - Semua methods dilindungi middleware 'admin'
 * - Admin handle payment status updates
 * - Customer view invoices via CustomerDashboardController
 * 
 * Validation Rules:
 * - status: enum(draft, issued, paid, overdue)
 * - paid_date: required if status='paid', otherwise nullable
 * - Auto-set paid_date = now() jika status=paid tanpa paid_date
 * 
 * Delete Restriction:
 * - HANYA invoice dengan status='draft' bisa didelete
 * - Issued/Paid/Overdue invoice TIDAK boleh delete (audit trail)
 * - Soft delete untuk preserve history
 */
class InvoiceController extends Controller
{
    /**
     * List semua invoice dengan pagination dan eager loading
     * 
     * Eager load relations:
     * - booking.customer: Siapa yang perlu bayar
     * - booking.service: Apa service yang dibayar
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Paginate 10 invoices per page with relations
        // Eager load booking relations untuk avoid N+1
        $invoices = Invoice::with('booking.customer', 'booking.service')->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show detail satu invoice dengan semua relasi
     * 
     * Eager load:
     * - booking.customer: Customer yang membayar
     * - booking.service: Service detail
     * - booking.technician: Teknisi yang kerjakan
     * 
     * Detail yang ditampilkan:
     * - Invoice number & dates
     * - Customer info & address
     * - Service detail & booking info
     * - Payment breakdown (subtotal, tax, total)
     * - Current payment status
     * - Technician yang kerjakan
     * 
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function show(Invoice $invoice)
    {
        // Authorization: Jika customer, hanya bisa lihat own invoice
        if (auth()->user()->role === 'customer') {
            $customer = Customer::where('email', auth()->user()->email)->first();
            if (!$customer || $invoice->booking->customer_id !== $customer->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini');
            }
        }
        
        // Load all related data for detail view
        $invoice->load('booking.customer', 'booking.service', 'booking.technician');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show form untuk edit invoice status dan paid_date
     * 
     * Form fields:
     * - status: Dropdown enum(draft, issued, paid, overdue)
     * - paid_date: Date input (required if status=paid)
     * 
     * Use case:
     * - Admin review draft invoice & issue
     * - Admin update status to paid setelah terima pembayaran
     * - Admin update to overdue jika melewat deadline
     * 
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update invoice status dan paid_date
     * 
     * Validasi:
     * - status: required, enum(draft, issued, paid, overdue)
     * - paid_date: nullable normally, required if status='paid'
     *   (conditional validation: required_if:status,paid)
     * 
     * Logic:
     * 1. Validasi input
     * 2. Jika status mau di-set paid dan belum ada paid_date
     *    -> Auto-set paid_date = now()
     * 3. Update invoice
     * 
     * Use Case Scenarios:
     * - draft -> issued: Admin send to customer
     * - issued -> paid: Customer bayar, admin confirm
     * - issued -> overdue: After due date check
     * - draft -> delete: Review ditolak (via destroy)
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Validasi input
        $validated = $request->validate([
            'status' => 'required|in:draft,issued,paid,overdue',
            'paid_date' => 'nullable|date|required_if:status,paid',
        ]);

        // Auto-set paid_date jika status=paid dan belum ada
        if ($validated['status'] === 'paid' && !$invoice->paid_date) {
            $validated['paid_date'] = now();
        }

        // Update invoice
        $invoice->update($validated);
        
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil diperbarui');
    }

    /**
     * Quick action: Mark invoice sebagai PAID tanpa form
     * 
     * Convenience method untuk cepat update paid status
     * Langsung set:
     * - status = 'paid'
     * - paid_date = now() (current timestamp)
     * 
     * Use Case:
     * - Cash/Direct payment received
     * - Bank transfer confirmed
     * - Need quick mark as paid tanpa masuk edit form
     * 
     * Redirect:
     * - Back to invoice detail page
     * - Show success message
     * 
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsPaid(Invoice $invoice)
    {
        // Update status & timestamp
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);
        
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil ditandai sebagai dibayar');
    }

    /**
     * Delete (soft delete) invoice dari database
     * 
     * Restriction: HANYA invoice dengan status 'draft' yang boleh delete
     * Reason: Issued/Paid/Overdue invoice harus tetap terekam untuk audit trail
     * 
     * Soft Delete Behavior:
     * - Set deleted_at = now()
     * - Data tetap ada di database
     * - Query normal tidak return deleted records
     * - Bisa di-restore jika perlu
     * 
     * Draft Invoice:
     * - Invoice baru yang masih dalam review
     * - Belum dikirim ke customer
     * - Boleh didelete jika ada kesalahan
     * 
     * Error Handling:
     * - Jika status != 'draft' -> return error
     * - Redirect back ke show page dengan error message
     * - Invoice tetap ada (tidak deleted)
     * 
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Invoice $invoice)
    {
        // Check: hanya draft yang boleh delete
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Hanya invoice draft yang dapat dihapus');
        }
        
        // Soft delete
        $invoice->delete();
        
        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus');
    }
}
