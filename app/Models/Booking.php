<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Booking Model
 * 
 * Model untuk mengelola pemesanan (booking) servis AC.
 * Merepresentasikan transaksi servis antara customer, service, dan technician.
 * 
 * Status Workflow:
 * pending -> confirmed -> in_progress -> completed
 * atau bisa di-cancel dari status manapun.
 * 
 * Relasi:
 * - belongsTo('customer'): Booking dimiliki oleh satu customer
 * - belongsTo('service'): Booking menggunakan satu service
 * - belongsTo('technician'): Booking ditangani oleh satu technician
 * - hasOne('invoice'): Booking memiliki satu invoice
 * 
 * @property int $id
 * @property int $customer_id FK ke customers table
 * @property int $service_id FK ke services table
 * @property int|null $technician_id FK ke technicians table (null jika belum assigned)
 * @property datetime $scheduled_date Tanggal & jam jadwal servis
 * @property string $notes Catatan dari customer
 * @property string $status Status booking (pending/confirmed/in_progress/completed/cancelled)
 * @property decimal $total_price Harga total servis
 * @property string|null $completion_notes Catatan dari technician setelah selesai
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at (SoftDelete)
 */
class Booking extends Model
{
    use SoftDeletes; // Soft delete untuk audit trail

    /**
     * Atribut yang dapat di-assign secara massal
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',      // ID pelanggan yang booking
        'service_id',       // ID layanan yang dipesan
        'technician_id',    // ID teknisi yang ditugaskan (nullable)
        'scheduled_date',   // Tanggal & waktu jadwal servis
        'notes',            // Catatan/keterangan dari customer
        'status',           // Status booking
        'total_price',      // Harga total (bisa berbeda dari service.price jika ada adjustments)
        'completion_notes', // Catatan hasil servis dari teknisi
    ];

    /**
     * Type casting untuk kolom tertentu
     * 
     * @var array<int, string>
     */
    protected $casts = [
        'scheduled_date' => 'datetime', // Auto-convert ke Carbon instance
        'total_price' => 'decimal:2',   // 2 decimal places untuk currency
    ];

    /**
     * Relasi: Booking belongsTo Customer
     * Inverse relation dari Customer::bookings()
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi: Booking belongsTo Service
     * Menunjuk ke service apa yang dipesan
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relasi: Booking belongsTo Technician
     * Nullable - technician diberikan after booking confirmed
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * Relasi: Booking hasOne Invoice
     * Setiap booking bisa memiliki 1 invoice (untuk payment tracking)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Relasi: Booking hasOne Rating
     * Setiap booking bisa memiliki 1 rating dari customer
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    // ===== STATUS HELPER METHODS =====
    // Methods ini memudahkan pengecekan status booking tanpa hardcoding string

    /**
     * Check apakah booking masih dalam status pending (menunggu konfirmasi)
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check apakah booking sudah dikonfirmasi
     */
    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check apakah booking sedang dalam proses
     */
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check apakah booking sudah selesai
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check apakah booking sudah dibatalkan
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
