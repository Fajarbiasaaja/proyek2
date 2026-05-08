<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Invoice Model
 * 
 * Model untuk mengelola invoice/tagihan pembayaran atas booking servis.
 * Setiap booking akan generate invoice untuk tracking pembayaran.
 * 
 * Status Payment Flow:
 * pending -> processing -> paid
 * atau overdue jika lewat due_date
 * 
 * Relasi:
 * - belongsTo('booking'): Invoice terkait dengan satu booking
 * - through('booking'): Akses customer melalui booking
 * 
 * @property int $id
 * @property int $booking_id FK ke bookings table
 * @property string $invoice_number Nomor invoice unik (format: INV-YYYYMMDD-001)
 * @property decimal $subtotal Total sebelum pajak
 * @property decimal $tax Jumlah pajak
 * @property decimal $total Total akhir (subtotal + tax)
 * @property string $status Status pembayaran (pending/processing/paid/overdue)
 * @property datetime $due_date Batas waktu pembayaran
 * @property datetime|null $paid_date Tanggal pembayaran (diisi saat status jadi paid)
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at (SoftDelete)
 */
class Invoice extends Model
{
    use SoftDeletes; // Soft delete untuk audit trail pembayaran

    /**
     * Atribut yang dapat di-assign secara massal
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',           // ID booking yang terkait
        'invoice_number',       // Nomor invoice (unique identifier)
        'subtotal',             // Total tanpa pajak
        'tax',                  // Besaran pajak/PPN
        'total',                // Total akhir (subtotal + tax)
        'status',               // Status pembayaran
        'due_date',             // Batas waktu pembayaran
        'paid_date',            // Tanggal pembayaran (jika sudah dibayar)
    ];

    /**
     * Type casting untuk kolom tertentu
     * 
     * @var array<int, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',  // Currency format
        'tax' => 'decimal:2',       // Currency format
        'total' => 'decimal:2',     // Currency format
        'due_date' => 'datetime',   // Auto-convert ke Carbon instance
        'paid_date' => 'datetime',  // Auto-convert ke Carbon instance
    ];

    /**
     * Relasi: Invoice belongsTo Booking
     * Setiap invoice terkait dengan satu booking
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relasi: Access customer melalui booking relationship
     * Shortcut: $invoice->customer() instead of $invoice->booking->customer
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function customer()
    {
        return $this->through('booking')->has('customer');
    }

    /**
     * Relasi: Invoice hasMany payments
     * 
     * Satu invoice bisa punya multiple payment attempts:
     * - Customer submit payment, rejected -> resubmit
     * - Customer bayar partial, then submit sisa
     * - Audit trail: track semua payment attempts
     * 
     * Eager load dengan: $invoice->load('payments')
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relasi: Invoice hasMany approved payments only
     * 
     * Quick access ke approved (final) payments
     * Useful untuk payment confirmation & receipts
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvedPayments()
    {
        return $this->hasMany(Payment::class)->where('status', 'approved');
    }

    /**
     * Get latest payment submission (pending or approved)
     * 
     * Useful untuk tracking latest payment status
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function latestPayment()
    {
        return $this->payments()->latest('created_at')->first();
    }

    // ===== STATUS HELPER METHODS =====

    /**
     * Check apakah invoice sudah dibayar
     * 
     * @return bool
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check apakah invoice sudah lewat batas waktu pembayaran
     * 
     * @return bool
     */
    public function isOverdue()
    {
        return $this->status === 'overdue';
    }

    /**
     * Check apakah invoice masih pending pembayaran
     * 
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
