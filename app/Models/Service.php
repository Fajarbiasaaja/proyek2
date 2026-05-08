<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Service Model
 * 
 * Model untuk mengelola daftar layanan servis AC yang ditawarkan.
 * Setiap service memiliki harga dan durasi estimasi.
 * 
 * Relasi:
 * - hasMany('bookings'): Satu service bisa di-booking berkali-kali
 * 
 * @property int $id
 * @property string $name Nama layanan (contoh: "Pembersihan AC")
 * @property string|null $description Deskripsi detail layanan
 * @property decimal $price Harga layanan (format currency)
 * @property int $duration_minutes Estimasi durasi dalam menit
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at (SoftDelete)
 */
class Service extends Model
{
    use SoftDeletes; // Soft delete untuk keperluan arsip

    /**
     * Atribut yang dapat di-assign secara massal
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',              // Nama service
        'description',       // Deskripsi panjang
        'price',             // Harga service
        'duration_minutes',  // Berapa lama service ini (dalam menit)
    ];

    /**
     * Type casting untuk kolom tertentu
     * 
     * @var array<int, string>
     */
    protected $casts = [
        'price' => 'decimal:2', // Currency dengan 2 decimal places
    ];

    /**
     * Relasi: Service hasMany Bookings
     * Satu service bisa di-booking berkali-kali oleh customers berbeda
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
