<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Rating Model
 * 
 * Model untuk mengelola rating dan review yang diberikan customer terhadap booking/service.
 * Digunakan untuk menampilkan reputasi technician dan service quality.
 * 
 * Relasi:
 * - belongsTo('booking'): Rating milik satu booking
 * - belongsTo('customer'): Rating dibuat oleh satu customer
 * - belongsTo('technician'): Rating untuk satu technician
 * 
 * @property int $id
 * @property int $booking_id FK ke bookings table
 * @property int $customer_id FK ke customers table
 * @property int $technician_id FK ke technicians table
 * @property int $rating Rating score (1-5 stars)
 * @property string|null $review Review text dari customer
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at (SoftDelete)
 */
class Rating extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'technician_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke Booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relasi ke Customer (pemberi rating)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi ke Technician (penerima rating)
     */
    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * Scope untuk mendapatkan rating dengan score tertentu
     */
    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope untuk rating terbaru
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Hitung rating average untuk satu technician
     */
    public static function averageForTechnician($technicianId)
    {
        return self::where('technician_id', $technicianId)
            ->avg('rating') ?? 0;
    }

    /**
     * Hitung total rating count untuk satu technician
     */
    public static function countForTechnician($technicianId)
    {
        return self::where('technician_id', $technicianId)->count();
    }
}
