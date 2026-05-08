<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateSlidersTable Migration
 * 
 * Tabel untuk menyimpan carousel/slider images yang tampil di dashboard.
 * Slider adalah feature UI yang menampilkan promotional images yang auto-rotate.
 * 
 * Fitur:
 * - Auto-sliding carousel di dashboard (rotate setiap 5 detik)
 * - Manual navigation (previous/next buttons)
 * - Paginasi indicators (dot indicators menunjukkan slide aktif)
 * - Admin dapat manage slides (create, edit, delete, reorder)
 * - Toggle active/inactive tanpa perlu edit full form
 * 
 * Content Management:
 * - title: Judul slide (tampil sebagai heading)
 * - description: Deskripsi detail slide (tampil sebagai text)
 * - image: Path ke image file (disimpan di storage/public/sliders/)
 * - button_text: Text button CTA (Call-To-Action)
 * - button_link: URL yang di-link button (untuk navigation ke halaman lain)
 * 
 * Display & Ordering:
 * - sort_order: Integer untuk urutan tampilan (0, 1, 2, dst)
 *   Slide dengan sort_order lebih kecil tampil duluan
 *   Admin dapat reorder slides dengan drag-drop
 * 
 * Visibility Control:
 * - is_active: Boolean untuk show/hide slide
 *   true = tampil di carousel
 *   false = hidden dari carousel (tapi data tetap tersimpan)
 *   Use case: Matikan slide seasonal tanpa perlu delete
 * 
 * Implementation Detail di DashboardController:
 * - Slider::query()->where('is_active', true)
 *   ->orderBy('sort_order')
 *   ->get()
 * - Hanya ambil active sliders yang sudah di-order
 * 
 * File Handling:
 * - Image disimpan di storage/public/sliders/FILENAME
 * - SliderController::store() upload image & simpan path
 * - SliderController::update() jika ada image baru, delete yang lama
 * - SliderController::destroy() hapus slider & delete image file
 * - $slider->bootd() event listener otomatis delete image saat slider deleted
 * 
 * Admin Interface:
 * - List view: Paginate 10 per page, dengan preview image + action buttons
 * - Create: Form upload image, text fields untuk title/description/button
 * - Edit: Preview existing image, upload new image replacement
 * - Delete: Confirm dialog, delete image file dari disk
 * - Toggle: Quick action tanpa form, hanya ubah is_active status
 * 
 * Implementasi di Model:
 * @see Slider model dengan getActive() scope & booted() event
 * @see SliderController untuk CRUD operations
 * @see DashboardController mempassing active sliders ke view
 * @see views/dashboard.blade.php carousel HTML & auto-scroll JavaScript
 */
return new class extends Migration
{
    /**
     * Run the migrations - create sliders table
     */
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            // Primary key - unique identifier untuk setiap slide
            $table->id();
            
            // Judul slide (heading yang tampil di carousel)
            $table->string('title')->nullable()
                  ->comment('Slide title/heading displayed in carousel');
            
            // Deskripsi detail slide dengan text panjang
            $table->longText('description')->nullable()
                  ->comment('Detailed description text shown on slide');
            
            // Path ke image file (relative path ke storage/public/sliders/)
            $table->string('image')
                  ->comment('Image file path: storage/public/sliders/filename');
            
            // Text untuk button CTA (Call-To-Action)
            $table->string('button_text')->nullable()
                  ->comment('Call-to-action button text (e.g., "Booking Now", "Learn More")');
            
            // URL destination saat button di-click
            $table->string('button_link')->nullable()
                  ->comment('Button click destination URL (e.g., /booking, https://external-link)');
            
            // Urutan tampilan slide di carousel
            $table->integer('sort_order')->default(0)
                  ->comment('Display order in carousel (asc) - lower numbers appear first');
            
            // Visibility flag - tampil atau hidden
            $table->boolean('is_active')->default(true)
                  ->comment('Active flag: true=visible in carousel, false=hidden but not deleted');
            
            // Timestamps untuk audit
            // created_at & updated_at for slide creation/modification tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations - drop sliders table
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
