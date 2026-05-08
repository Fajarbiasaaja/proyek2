<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Technician Model
 * 
 * Model untuk mengelola data teknisi/mekanik yang bekerja di perusahaan.
 * Teknisi ditugaskan untuk menangani booking/servis dari customers.
 * 
 * Status:
 * - available: Teknisi siap menerima pekerjaan
 * - busy: Sedang melakukan pekerjaan
 * - off: Sedang libur/cuti
 * 
 * Relasi:
 * - hasMany('bookings'): Satu teknisi bisa menangani banyak booking
 * 
 * @property int $id
 * @property int $user_id Foreign key ke users table
 * @property string $name Nama lengkap teknisi
 * @property string $phone Nomor telepon/HP
 * @property string $email Email untuk komunikasi
 * @property string $address Alamat tempat tinggal
 * @property string|null $specialization Keahlian khusus (contoh: "AC Central")
 * @property string $status Status ketersediaan (available/busy/off)
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at (SoftDelete)
 */
class Technician extends Model
{
    use SoftDeletes; // Soft delete untuk arsip teknisi

    /**
     * Atribut yang dapat di-assign secara massal
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',          // Foreign key ke users table
        'name',             // Nama teknisi
        'phone',            // Nomor kontak
        'email',            // Email
        'address',          // Alamat rumah
        'specialization',   // Keahlian khusus (optional)
        'status',           // Status availability (available/busy/off)
    ];

    /**
     * Relasi: Technician belongsTo User
     * Setiap teknisi terhubung ke satu user account (role='technician')
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Technician hasMany Bookings
     * Satu teknisi bisa menangani banyak booking
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relasi: Get available bookings untuk teknisi ini
     * Mengambil bookings yang masih pending atau confirmed (work in progress)
     * 
     * Scope ini berguna untuk mengetahui workload teknisi saat ini
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availableBookings()
    {
        return $this->bookings()->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Relasi: Technician hasMany Ratings
     * Satu teknisi bisa menerima banyak rating dari customers
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
