<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Slider Model
 * 
 * Model untuk mengelola carousel/banner image di dashboard.
 * Admin dapat upload, edit, dan manage sliders dari admin panel.
 * 
 * Fitur:
 * - Auto-slide di dashboard (setiap 5 detik)
 * - Responsive design untuk semua ukuran layar
 * - Optional call-to-action buttons dengan custom link
 * - Sort order untuk mengatur urutan tampil
 * - Enable/disable untuk kontrol visibility
 * 
 * @property int $id
 * @property string $title Judul slider (ditampilkan di overlay)
 * @property string $description Deskripsi/subtitle slider
 * @property string $image Path ke image file (stored di storage/public/sliders/)
 * @property string|null $button_text Teks tombol CTA (optional)
 * @property string|null $button_link URL target tombol CTA (optional)
 * @property int $sort_order Urutan tampil (0 = pertama, ascending)
 * @property bool $is_active Flag aktif/nonaktif untuk visibility di dashboard
 * @property timestamp $created_at
 * @property timestamp $updated_at
 */
class Slider extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat di-assign secara massal
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'title',           // Judul slider
        'description',     // Deskripsi panjang
        'image',           // Path ke file image
        'button_text',     // Teks tombol CTA
        'button_link',     // URL link tombol
        'sort_order',      // Urutan tampil
        'is_active',       // Status active/inactive
    ];

    /**
     * Type casting untuk kolom tertentu
     * 
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',    // Cast to boolean (0/1 to true/false)
        'sort_order' => 'integer',   // Cast to integer
    ];

    /**
     * Get semua slider yang aktif dan siap ditampilkan
     * 
     * Query ini digunakan di DashboardController untuk mengambil sliders
     * yang akan ditampilkan di carousel dashboard.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Event: Auto-delete image file saat slider dihapus
     * 
     * Ini memastikan storage tidak penuh dengan orphan image files.
     * Jika slider dihapus, image file di storage juga ikut dihapus.
     * 
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($slider) {
            // Check jika image file ada di storage
            if ($slider->image && file_exists(public_path('storage/' . $slider->image))) {
                // Hapus file dari disk
                unlink(public_path('storage/' . $slider->image));
            }
        });
    }
}
