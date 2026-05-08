<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreatePaymentsTable Migration
 * 
 * Tabel untuk mencatat setiap transaksi pembayaran dari customer ke sistem.
 * Dengan sistem approval dari admin untuk validation & audit trail.
 * 
 * Payment Workflow:
 * 1. Customer submit pembayaran via payment form
 *    -> Include invoice reference, amount, payment method, proof/reference
 *    -> Payment created dengan status 'pending_approval'
 * 
 * 2. Admin review pending payments
 *    -> Check amount match invoice
 *    -> Verify payment reference/proof
 *    -> Approve atau reject payment
 * 
 * 3. Approved:
 *    -> Payment status = 'approved'
 *    -> Invoice status = 'paid', paid_date = now()
 *    -> Customer notified (payment confirmed)
 *    -> Accounting record created
 * 
 * 4. Rejected:
 *    -> Payment status = 'rejected'
 *    -> reason/notes untuk customer (why rejected)
 *    -> Customer dapat resubmit
 *    -> Invoice tetap unpaid, remind customer to retry
 * 
 * Status Workflow:
 * pending_approval (initial)
 *         |
 *         +-----> approved (paid, invoice updated)
 *         |
 *         +-----> rejected (failed validation, customer resubmit)
 * 
 * Payment Methods:
 * - cash: Direct payment (di service location)
 * - bank_transfer: Via bank (BCA, Mandiri, BNI, etc)
 * - credit_card: Via credit card (Visa, Mastercard)
 * - e_wallet: Via e-wallet (GCash, Dana, etc)
 * - check: Via post-dated/crossed check
 * 
 * Relasi:
 * - belongsTo invoice: Satu payment untuk satu invoice
 * - belongsTo approver_user: Admin yang approve payment ini
 * 
 * Duplicate Prevention:
 * - Multiple payment attempts untuk same invoice tracked
 * - Status pending = customer waiting for admin approval
 * - Status rejected = customer can resubmit dengan corrected data
 * - Status approved = invoice locked sebagai paid
 * 
 * Audit Trail:
 * - created_at: Timestamp pembayaran di-submit
 * - updated_at: Timestamp approval/rejection
 * - approved_by: User ID admin yang approve/reject
 * - approved_date: Timestamp actual approval
 * - notes: Reason (jika reject) atau payment confirmation details
 * 
 * Reference Tracking:
 * - reference_number: Bank transfer ref, check number, CC transaction ID
 * - payment_proof: File path ke bukti transfer (screenshot, receipt)
 * - Untuk verification dan customer reference
 * 
 * Amount Validation:
 * - amount: Payment amount (bisa partial atau full)
 * - invoice->total: Total amount di-invoice
 * - Allow partial payment jika customer negotiate
 * - Admin validate amount sebelum approve
 * 
 * Implementation di Model:
 * @see Payment model dengan payment status helpers
 * @see Invoice model dengan hasMany payments relationship
 * @see User model approved_by relationship
 * 
 * Routes:
 * - Customers POST /payments (submit payment)
 * - Customers GET /invoices/:id/payment-form (payment form)
 * - Admin GET /admin/payments (list pending)
 * - Admin POST /admin/payments/:id/approve (approve)
 * - Admin POST /admin/payments/:id/reject (reject)
 * - Admin GET /admin/payments/:id (view detail)
 */
return new class extends Migration
{
    /**
     * Run the migrations - create payments table
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Foreign key ke invoice yang dibayar
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade')
                  ->comment('Invoice yang dibayar via payment ini');
            
            // Nominal pembayaran
            $table->decimal('amount', 10, 2)
                  ->comment('Payment amount in Rupiah (allow partial payment)');
            
            // Metode pembayaran
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'e_wallet', 'check'])->default('bank_transfer')
                  ->comment('Payment method: cash, bank_transfer, credit_card, e_wallet, check');
            
            // Nomor referensi pembayaran (bank transfer ref, check number, CC ID)
            $table->string('reference_number')->nullable()
                  ->comment('Bank transfer reference number, check number, or CC transaction ID');
            
            // File path ke bukti pembayaran (screenshot transfer, receipt, dll)
            $table->string('payment_proof')->nullable()
                  ->comment('File path ke payment proof (screenshot, receipt, etc)');
            
            // Tanggal pembayaran di-submit oleh customer
            $table->dateTime('submitted_date')->nullable()
                  ->comment('Timestamp saat customer submit payment');
            
            // Status approval payment
            $table->enum('status', ['pending_approval', 'approved', 'rejected'])->default('pending_approval')
                  ->comment('Payment approval status');
            
            // Foreign key ke user (admin) yang approve/reject
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Admin user_id yang approve atau reject payment');
            
            // Tanggal approval/rejection
            $table->dateTime('approved_date')->nullable()
                  ->comment('Timestamp saat payment di-approve atau di-reject');
            
            // Catatan dari admin (approval confirmation atau rejection reason)
            $table->text('notes')->nullable()
                  ->comment('Admin notes: confirmation details atau rejection reason');
            
            // Timestamps untuk audit
            // created_at & updated_at for payment submission/modification tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations - drop payments table
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
