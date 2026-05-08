<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Customer Model
 * 
 * Model untuk mengelola data pelanggan yang terdaftar di sistem.
 * Setiap customer dapat melakukan multiple bookings (pemesanan servis).
 * 
 * Relasi:
 * - hasMany('bookings'): Satu customer bisa memiliki banyak bookings
 * - hasManyThrough('invoices'): Customer memiliki invoices melalui bookings
 * 
 * @property int $id
 * @property string $name Nama lengkap pelanggan
 * @property string $email Email pelanggan
 * @property string $phone Nomor hp/telepon pelanggan
 * @property string $address Alamat rumah/kantor pelanggan
 * @property string|null $city Kota pelanggan
 * @property string|null $postal_code Kode pos pelanggan
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at (SoftDelete)
 */
class Customer extends Model
{
    use SoftDeletes; // Memungkinkan soft delete (data tidak dihapus fisik, hanya di-flag)

    /**
     * Atribut yang dapat di-assign secara massal
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',        // Nama lengkap
        'phone',       // Nomor telpon
        'email',       // Email
        'address',     // Alamat
        'city',        // Kota
        'postal_code', // Kode pos
    ];

    /**
     * Relasi: Customer hasMany Bookings
     * 
     * Seorang customer dapat memiliki banyak bookings (pemesanan servis).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relasi: Customer hasManyThrough Invoices
     * 
     * Customer memiliki invoices melalui bookings.
     * Digunakan untuk mengambil semua invoice pelanggan dengan dalam satu query.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Booking::class);
    }
}
