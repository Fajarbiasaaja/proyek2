<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateInvoicesTable Migration
 * 
 * Tabel untuk menyimpan invoice/tagihan untuk setiap completed booking.
 * Invoice adalah dokumen finansial yang merepresentasikan transaksi pembayaran.
 * 
 * Relasi:
 * - belongsTo booking: Setiap invoice terikat ke satu booking
 * - Through booking -> customer: Untuk mengetahui siapa customer yang dibayar
 * - Foreign key: booking_id dengan CASCADE behavior
 * 
 * Status Workflow:
 * - draft: Belum di-issue ke customer (dalam proses)
 * - issued: Invoice sudah dikirim ke customer, menunggu pembayaran
 * - paid: Invoice sudah dibayar (pembayaran diterima)
 * - overdue: Invoice sudah lewat due date, belum dibayar
 * 
 * Payment Flow:
 * 1. Booking completed -> Invoice dibuat dengan status draft
 * 2. Admin review & issue -> Status menjadi issued
 * 3. Customer bayar sebelum due_date -> Status menjadi paid
 * 4. Jika belum bayar after due_date -> Status menjadi overdue
 * 
 * Finansial Breakdown:
 * - subtotal: Harga service dari booking
 * - tax: Pajak (PPN 10% atau sesuai aturan pasal 17 UU PPh)
 * - total: subtotal + tax = jumlah yang harus dibayar
 * - invoice_number: Nomor invoice unik untuk referensi (e.g., INV-2025-001)
 * 
 * CASCADE Delete Behavior:
 * - Jika booking dihapus, invoice otomatis terhapus (soft delete)
 * - Alasan: Invoice tidak bisa exist tanpa booking parent
 * - Data tetap terekam karena soft delete (untuk audit)
 * 
 * Implementasi di Model:
 * @see Invoice model dengan payment helper methods (isPaid, isOverdue, isPending)
 * @see Booking::invoice relationship (one-to-one)
 * @see Customer dapat akses invoices via hasMany through booking
 */
return new class extends Migration
{
    /**
     * Run the migrations - create invoices table
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            // Primary key - unique identifier untuk setiap invoice
            $table->id();
            
            // Foreign key ke booking
            // CASCADE: Jika booking dihapus, invoice juga terhapus
            $table->foreignId('booking_id')->constrained()->onDelete('cascade')
                  ->comment('Foreign key to bookings table. CASCADE: delete invoice if booking deleted');
            
            // Nomor invoice unik untuk referensi
            $table->string('invoice_number')->unique()
                  ->comment('Unique invoice number for tracking (e.g., INV-2025-001)');
            
            // Subtotal sebelum pajak (dari service price di booking)
            $table->decimal('subtotal', 10, 2)
                  ->comment('Service price from booking (before tax)');
            
            // Nilai pajak yang dikenakan
            $table->decimal('tax', 10, 2)->default(0)
                  ->comment('Tax amount (usually 10% PPN per Indonesian law)');
            
            // Total yang harus dibayar = subtotal + tax
            $table->decimal('total', 10, 2)
                  ->comment('Total amount to be paid = subtotal + tax');
            
            // Status pembayaran invoice
            $table->enum('status', ['draft', 'issued', 'paid', 'overdue'])->default('draft')
                  ->comment('Payment status: draft (in process), issued (sent to customer), paid (received), overdue (past due date)');
            
            // Tanggal deadline pembayaran
            $table->dateTime('due_date')->nullable()
                  ->comment('Payment deadline - null if not set, otherwise overdue if not paid by this date');
            
            // Tanggal pembayaran diterima
            $table->dateTime('paid_date')->nullable()
                  ->comment('Timestamp when payment was received - null if not yet paid');
            
            // Timestamps untuk audit
            // created_at & updated_at for invoice creation/modification tracking
            $table->timestamps();
            
            // Soft delete untuk archive
            $table->softDeletes()
                  ->comment('deleted_at: invoice tetap tersimpan saat di-delete untuk audit trail');
        });
    }

    /**
     * Reverse the migrations - drop invoices table
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
