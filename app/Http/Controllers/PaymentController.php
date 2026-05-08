<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * PaymentController - Payment Transaction Management dengan Admin Approval
 * 
 * Handles payment submission dari customer dan approval workflow dari admin.
 * Dengan tracking, validation, dan audit trail untuk semua transactions.
 * 
 * Customer Routes:
 * - showPaymentForm: Display form untuk submit payment untuk specific invoice
 * - submitPayment: Process payment submission
 * - paymentHistory: View payment history untuk invoice
 * 
 * Admin Routes:
 * - listPendingPayments: List semua pending approval payments
 * - approvePayment: Admin approve payment
 * - rejectPayment: Admin reject payment
 * - paymentDetail: View detail satu payment
 * 
 * Payment Workflow:
 * ==================
 * 
 * CUSTOMER SIDE:
 * 1. Customer lihat invoice detail -> See "Pay Now" button
 * 2. Click -> Redirect ke payment form (showPaymentForm)
 * 3. Fill form: amount, payment method, reference number, upload proof
 * 4. Submit -> Create payment dengan status 'pending_approval'
 * 5. Message: "Payment submitted, waiting for admin approval"
 * 6. Check payment status via history atau dashboard
 * 
 * ADMIN SIDE:
 * 1. Admin access /admin/payments/pending
 * 2. See list payments waiting approval
 * 3. Click payment -> View detail (paymentDetail)
 * 4. Verify: amount match invoice, proof valid, reference correct
 * 5a. APPROVE: 
 *     -> Payment status = 'approved'
 *     -> Invoice status = 'paid'
 *     -> Invoice paid_date = now()
 *     -> Message: "Payment approved, invoice marked as paid"
 * 5b. REJECT:
 *     -> Payment status = 'rejected'
 *     -> Notes = rejection reason visual
 *     -> Invoice tetap 'issued' (unpaid)
 *     -> Message: "Payment rejected, customer notified"
 * 
 * Payment Validation:
 * ==================
 * - amount: Required, numeric, min 0, max invoice->total
 * - payment_method: Required, enum (cash, bank_transfer, credit_card, e_wallet, check)
 * - reference_number: Required untuk non-cash methods
 * - payment_proof: File upload (image/pdf) untuk proof
 * - submitted_date: Optional (default = now())
 * 
 * Use Cases:
 * =========
 * 
 * 1. Online Banking (Most Common):
 *    - Payment method: bank_transfer
 *    - Reference: Bank reference number
 *    - Proof: Screenshot transfer confirmation
 *    - Customer submit -> Admin verify at bank -> Approve
 * 
 * 2. Cash Payment:
 *    - Payment method: cash
 *    - Reference: Receipt/slip number
 *    - Proof: Receipt photo
 *    - Technician collect cash -> Admin process -> Approve
 * 
 * 3. Partial Payment:
 *    - Amount < invoice->total
 *    - Create multiple payments to settle
 *    - Each payment separate approval
 *    - Invoice status 'paid' only when total covered
 * 
 * 4. Rejected Payment:
 *    - Admin reject karena: amount not match, invalid proof, duplicate
 *    - Payment stays in REJECTED status
 *    - Customer resubmit dengan corrected info
 *    - New payment = new transaction (separate record)
 * 
 * File Handling:
 * ==============
 * - payment_proof: Upload ke storage/public/payments/
 * - Naming: payment-{PAYMENT_ID}-{FILENAME}
 * - On delete (if rejected): Delete file juga
 * - Validation: image (jpg,png) atau pdf
 * 
 * Admin Approval Logic:
 * ====================
 * 
 * Check:
 * 1. Amount valid (0 < amount <= invoice->total)
 * 2. Reference number ada & match payment method requirement
 * 3. Proof file valid & readable
 * 4. Not duplicate (check recent payments)
 * 
 * Then either:
 * - APPROVE: Update payment status, update invoice, notify customer
 * - REJECT: Set status, add reason, customer retry
 * 
 * Database Integrity:
 * ===================
 * - CASCADE delete: payment deleted if invoice deleted
 * - Payment->approved_by: nullable (pending = no approver yet)
 * - Payment->approved_date: nullable (filled after approval)
 * - Invoice->paid_date: set when payment approved
 * - One-to-many: Invoice can have multiple payments (before approval)
 */
class PaymentController extends Controller
{
    /**
     * Show payment form untuk submit payment untuk specific invoice
     * 
     * CUSTOMER VIEW
     * 
     * Ini adalah form yang customer lihat untuk submit pembayaran.
     * Pre-populate invoice info untuk reference.
     * 
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function showPaymentForm(Invoice $invoice)
    {
        // Auth check: customer hanya bisa submit payment untuk own invoice
        $user = Auth::user();
        if ($invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized to submit payment for this invoice');
        }

        // Load booking untuk customer info
        $invoice->load('booking.customer');
        
        return view('payments.payment-form', compact('invoice'));
    }

    /**
     * Submit payment untuk invoice
     * 
     * CUSTOMER ACTION
     * 
     * Supports both:
     * 1. Gateway payments (e-wallet, credit card) - Redirect to payment gateway
     * 2. Manual payments (cash, bank transfer, check) - Upload proof, wait admin approval
     * 
     * Validasi:
     * - amount: required, numeric, > 0, <= invoice->total
     * - payment_method: required, enum (cash, bank_transfer, credit_card, e_wallet, check)
     * - reference_number: required for non-cash (manual methods only)
     * - payment_proof: required for manual methods, optional for gateway
     * 
     * Flow untuk Gateway Payments (e-wallet, credit_card):
     * 1. Create payment record via PaymentGatewayService
     * 2. Service creates transaction di Midtrans
     * 3. Redirect customer ke payment_url
     * 4. Midtrans akan handle payment & send webhook
     * 5. Webhook auto-approves payment ketika settlement
     * 
     * Flow untuk Manual Payments (cash, bank_transfer, check):
     * 1. Customer submit dengan bukti pembayaran
     * 2. Create payment dengan status 'pending_approval'
     * 3. Admin verify & approve/reject
     * 4. After approve, invoice marked as paid
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitPayment(Request $request, Invoice $invoice)
    {
        // Auth check: customer hanya bisa submit untuk own invoice
        $user = Auth::user();
        if ($invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        // Validasi input
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->total,
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,e_wallet,check',
            'reference_number' => 'nullable|required_unless:payment_method,cash,credit_card,e_wallet|string|max:100',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:51200', // 50MB max
            'notes' => 'nullable|string|max:500',
        ], [
            'payment_proof.file' => 'File harus valid',
            'payment_proof.mimes' => 'Format file harus JPG, JPEG, PNG, atau PDF',
            'payment_proof.max' => 'Ukuran file maksimal 5MB',
        ]);

        // Tentukan apakah payment menggunakan gateway
        $gatewayService = new PaymentGatewayService();
        $isGatewayPayment = in_array($validated['payment_method'], ['e_wallet', 'credit_card']) 
            && PaymentGatewayService::isMidtransEnabled();

        // Handle Gateway Payments (redirect to payment gateway)
        if ($isGatewayPayment) {
            // Create payment via gateway service
            $result = $gatewayService->createPayment(
                $invoice,
                $validated['amount'],
                $validated['payment_method']
            );

            if ($result['success'] && !empty($result['payment_url'])) {
                // Redirect customer ke payment gateway
                return redirect($result['payment_url']);
            } else {
                // Jika gagal, return error
                return back()->with('error', $result['message'] ?? 'Gagal membuat payment');
            }
        }

        // Handle Manual Payments (upload proof, wait admin approval)
        // Untuk: cash, bank_transfer, check

        // Validasi untuk manual methods
        if (in_array($validated['payment_method'], ['bank_transfer', 'check'])) {
            // Require reference_number untuk non-cash
            if (empty($validated['reference_number'])) {
                return back()->withErrors(['reference_number' => 'Nomor referensi wajib untuk metode ini']);
            }
        }

        // Upload file jika ada
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            // Store file
            $file = $request->file('payment_proof');
            $filename = 'payment-' . now()->timestamp . '-' . $file->getClientOriginalName();
            $proofPath = $file->storeAs('payments', $filename, 'public');
        }

        // Create payment dengan status pending_approval
        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_gateway' => 'manual',
            'reference_number' => $validated['reference_number'] ?? null,
            'payment_proof' => $proofPath,
            'submitted_date' => now(),
            'status' => 'pending_approval', // Waiting admin approval
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('customer.dashboard')
            ->with('success', '✓ Pembayaran berhasil disubmit! Bukti telah dikirim ke admin untuk verifikasi.');
    }

    /**
     * View payment history untuk specific invoice
     * 
     * CUSTOMER VIEW
     * 
     * Customer dapat lihat semua payment attempts untuk invoice:
     * - Pending payments (waiting approval)
     * - Approved payments (confirmed)
     * - Rejected payments (failed, dapat retry)
     * 
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function paymentHistory(Invoice $invoice)
    {
        // Auth check
        $user = Auth::user();
        if ($invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        // Get all payments untuk invoice
        $payments = $invoice->payments()->latest('created_at')->get();

        return view('payments.payment-history', compact('invoice', 'payments'));
    }

    /**
     * List semua pending payments untuk admin review
     * 
     * ADMIN VIEW
     * 
     * Dashboard untuk admin melihat payments yang perlu approval
     * Dengan filter & pagination
     * 
     * @return \Illuminate\View\View
     */
    public function listPendingPayments()
    {
        // Get pending payments dengan eager load relations
        $payments = Payment::where('status', 'pending_approval')
            ->with('invoice.booking.customer')
            ->latest('created_at')
            ->paginate(10);

        return view('payments.admin-pending-list', compact('payments'));
    }

    /**
     * View detail payment untuk admin review sebelum approve/reject
     * 
     * ADMIN VIEW
     * 
     * Display payment detail dengan:
     * - Payment info (amount, method, reference, proof)
     * - Invoice detail (total, status, what service)
     * - Customer info (name, email, phone)
     * - Decision buttons (approve/reject)
     * 
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function paymentDetail(Payment $payment)
    {
        // Load all relations untuk detail view
        $payment->load('invoice.booking.customer', 'approver');

        return view('payments.admin-payment-detail', compact('payment'));
    }

    /**
     * Admin approve payment
     * 
     * ADMIN ACTION
     * 
     * Validasi & approve payment:
     * 1. Update payment: status='approved', approved_by=admin, approved_date=now()
     * 2. Update invoice: status='paid', paid_date=now()
     * 3. Add admin notes (optional)
     * 4. Success message
     * 
     * Business Logic:
     * - Check if approved, update parent invoice
     * - If partial payment: recalculate remaining balance
     * - Log approval untuk audit
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvePayment(Request $request, Payment $payment)
    {
        // Validasi: hanya pending payment bisa di-approve
        if (!$payment->isPending()) {
            return back()->with('error', 'Payment sudah di-process, tidak bisa diubah');
        }

        // Validasi input
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // Update payment
        $payment->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_date' => now(),
            'notes' => $validated['notes'] ?? 'Approved',
        ]);

        // Update parent invoice
        $invoice = $payment->invoice;
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return redirect()->route('payments.pending')->with('success', 'Pembayaran berhasil di-approve, invoice telah ditandai sebagai paid');
    }

    /**
     * Admin reject payment
     * 
     * ADMIN ACTION
     * 
     * Reject payment dengan reason untuk customer:
     * 1. Update payment: status='rejected', approved_by=admin, approved_date=now()
     * 2. Add notes: reason untuk rejection (displayed to customer)
     * 3. Invoice tetap unpaid
     * 4. Customer dapat resubmit dengan perubahan
     * 
     * Rejection Reasons (dalam notes):
     * - Amount tidak sesuai dengan invoice
     * - Reference number tidak valid/tidak ditemukan
     * - Proof tidak jelas/invalid
     * - Duplicate payment
     * - Payment method tidak terima
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectPayment(Request $request, Payment $payment)
    {
        // Validasi: hanya pending payment bisa di-reject
        if (!$payment->isPending()) {
            return back()->with('error', 'Payment sudah di-process, tidak bisa ditolak');
        }

        // Validasi input
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Update payment
        $payment->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_date' => now(),
            'notes' => $validated['reason'],
        ]);

        // Delete uploaded file jika ada
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        return redirect()->route('payments.pending')->with('success', 'Pembayaran ditolak, customer dapat resubmit dengan perubahan');
    }

    /**
     * Customer cancel pending payment (optional)
     * 
     * CUSTOMER ACTION
     * 
     * Allow customer untuk cancel payment yang masih pending
     * Useful kalau customer mau ganti payment method/amount
     * 
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelPayment(Payment $payment)
    {
        // Auth check
        $user = Auth::user();
        $invoice = $payment->invoice;
        if ($invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        // Validasi: hanya pending bisa di-cancel
        if (!$payment->isPending()) {
            return back()->with('error', 'Hanya pending payment yang bisa dibatalkan');
        }

        // Delete payment record
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }
        $payment->delete();

        return back()->with('success', 'Pembayaran dibatalkan, Anda dapat submit yang baru');
    }

    /**
     * Show payment progress/status (AJAX endpoint)
     * 
     * CUSTOMER VIEW
     * 
     * Return real-time payment status untuk progress tracking
     * Digunakan oleh progress indicator untuk live updates
     * 
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentProgress(Payment $payment)
    {
        // Auth check
        $user = Auth::user();
        if ($payment->invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        $progress = $payment->getProgressStatus();
        $buyerProtection = $payment->getBuyerProtectionInfo();

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'progress' => $progress,
            'buyer_protection' => $buyerProtection,
            'updated_at' => $payment->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Show payment receipt (digital receipt)
     * 
     * CUSTOMER VIEW
     * 
     * Display digital receipt untuk approved payment
     * Bisa di-print atau di-share
     * 
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function showReceipt(Payment $payment)
    {
        // Auth check
        $user = Auth::user();
        if ($payment->invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        // Load relations
        $payment->load('invoice.booking.customer', 'approver');

        return view('payments.payment-receipt', compact('payment'));
    }

    /**
     * Download payment receipt as PDF
     * 
     * CUSTOMER ACTION
     * 
     * Generate & download receipt PDF
     * 
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function downloadReceipt(Payment $payment)
    {
        // Auth check
        $user = Auth::user();
        if ($payment->invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        // Load relations
        $payment->load('invoice.booking.customer', 'approver');

        // Generate PDF (using DomPDF atau similar)
        // For now, return receipt view with printable styles
        $pdf = \PDF::loadView('payments.payment-receipt-pdf', compact('payment'));
        return $pdf->download('receipt-' . $payment->id . '.pdf');
    }

    /**
     * Show payment checkout page (unified checkout UI)
     * 
     * CUSTOMER VIEW
     * 
     * Modern unified checkout interface seperti Shopee
     * Dengan payment method selection dan real-time preview
     * 
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\View\View
     */
    public function showCheckout(Invoice $invoice)
    {
        // Auth check
        $user = Auth::user();
        if ($invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        // Load relations
        $invoice->load('booking.customer', 'booking.service', 'payments');

        // Calculate payment info
        $totalInvoice = $invoice->total;
        $paidAmount = $invoice->payments()->approved()->sum('amount');
        $remainingAmount = $totalInvoice - $paidAmount;

        return view('payments.payment-checkout', compact('invoice', 'remainingAmount', 'paidAmount'));
    }

    /**
     * View payment status tracking page
     * 
     * CUSTOMER VIEW
     * 
     * Page untuk track status pembayaran dengan progress indicator
     * Support real-time updates via AJAX
     * 
     * @param \App\Models\Payment $payment
     * @return \Illuminate\View\View
     */
    public function showProgress(Payment $payment)
    {
        // Auth check
        $user = Auth::user();
        if ($payment->invoice->booking->customer->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        // Load relations
        $payment->load('invoice.booking.customer');
        $progress = $payment->getProgressStatus();
        $buyerProtection = $payment->getBuyerProtectionInfo();

        return view('payments.payment-progress', compact('payment', 'progress', 'buyerProtection'));
    }
}
