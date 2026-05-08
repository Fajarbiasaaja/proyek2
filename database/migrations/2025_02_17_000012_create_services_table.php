<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateServicesTable Migration
 * 
 * Tabel untuk mendefinisikan service/layanan AC yang ditawarkan perusahaan.
 * Service adalah paket layanan yang dapat di-booking oleh customer.
 * 
 * Contoh Service:
 * - AC Maintenance Regular: Rp 150,000, durasi 30 menit
 * - AC Repair: Rp 250,000, durasi 60 menit
 * - AC Installation New: Rp 500,000, durasi 120 menit
 * - Freon Top-up: Rp 100,000, durasi 20 menit
 * 
 * Relasi:
 * - hasMany bookings: Satu service bisa dibooking berkali-kali oleh customer berbeda
 * - Referenced by Booking::service_id (foreignKey)
 * 
 * Business Logic:
 * - price adalah fixed price untuk service (bisa ada override per booking jika perlu)
 * - duration_minutes: Digunakan untuk scheduling & estimasi waktu kerja
 * - softDeletes: Archive service lama daripada delete langsung
 * - Harga dalam decimal(10,2) untuk support pembayaran dengan 2 desimal
 * 
 * Pricing Strategy:
 * - Setiap service memiliki harga standar
 * - Invoice akan menggunakan price ini sebagai dasar perhitungan
 * - Bisa di-override dalam booking jika ada negosiasi khusus
 * 
 * Implementasi di Model:
 * @see Service model dengan $casts untuk price sebagai decimal
 * @see Booking::service relationship (many-to-one)
 */
return new class extends Migration
{
    /**
     * Run the migrations - create services table
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            // Primary key - unique identifier untuk setiap service
            $table->id();
            
            // Nama service yang tampil ke customer
            $table->string('name')
                  ->comment('Service name displayed to customers (e.g., "AC Maintenance", "AC Repair")');
            
            // Deskripsi detail layanan
            $table->text('description')->nullable()
                  ->comment('Detailed description of what is included in this service');
            
            // Harga standar service dalam Rupiah
            $table->decimal('price', 10, 2)
                  ->comment('Fixed price for this service (2 decimal places for currency)');
            
            // Estimasi durasi service dalam menit
            $table->integer('duration_minutes')->default(60)
                  ->comment('Service duration in minutes for scheduling & time estimation');
            
            // Timestamps untuk audit trail
            // created_at & updated_at for record creation/modification tracking
            $table->timestamps();
            
            // Soft delete untuk archive service lama
            $table->softDeletes()
                  ->comment('deleted_at: ketika service di-archive, tetap ada di database');
        });
    }

    /**
     * Reverse the migrations - drop services table
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
