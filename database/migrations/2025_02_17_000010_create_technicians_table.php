<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateTechniciansTable Migration
 * 
 * Tabel untuk menyimpan data tenaga teknis (teknisi AC) yang tersedia di sistem.
 * Teknisi adalah staff/karyawan yang akan menangani service booking.
 * 
 * Relasi:
 * - hasMany bookings: Seorang teknisi bisa handle banyak booking
 * - Referenced by Booking::technician_id (foreignKey)
 * 
 * Status Workflow:
 * - available: Teknisi siap menerima booking baru
 * - busy: Teknis sedang ada pekerjaan, tidak bisa booking baru
 * - inactive: Teknisi tidak aktif (cuti, resign, dll)
 * 
 * Business Logic:
 * - email harus unique untuk komunikasi langsung dengan teknisi
 * - specialization: Tipe layanan yang dikuasai (AC Repair, AC Install, dll)
 * - softDeletes: Hapus teknisi tidak perlu delete langsung, bisa di-archive
 * - timestamps: Untuk audit trail kapan data dibuat/diubah
 * 
 * Implementasi di Model:
 * @see Technician model dengan scope availableBookings()
 * @see Booking::technician relationship (many-to-one)
 */
return new class extends Migration
{
    /**
     * Run the migrations - create technicians table
     */
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            // Primary key - unique identifier untuk setiap teknisi
            $table->id();
            
            // Nama lengkap teknisi
            $table->string('name')
                  ->comment('Full name of technician');
            
            // Nomor telepon untuk kontak
            $table->string('phone')
                  ->comment('Phone number for direct contact');
            
            // Email unik untuk komunikasi resmi
            $table->string('email')->unique()
                  ->comment('Email address - must be unique for official communication');
            
            // Alamat lengkap tempat tinggal
            $table->text('address')->nullable()
                  ->comment('Residential address for reference');
            
            // Keahlian/spesialisasi layanan
            $table->string('specialization')
                  ->comment('Service specialization (AC Repair, AC Installation, Maintenance, etc)');
            
            // Status ketersediaan teknisi
            $table->enum('status', ['available', 'busy', 'inactive'])->default('available')
                  ->comment('Availability status: available (ready for booking), busy (assigned), inactive (off/unavailable)');
            
            // Timestamps untuk audit trail
            // created_at & updated_at for record creation/modification tracking
            $table->timestamps();
            
            // Soft delete untuk archive teknisi
            $table->softDeletes()
                  ->comment('deleted_at: jika teknisi dihapus, data tetap di database dengan soft delete');
        });
    }

    /**
     * Reverse the migrations - drop technicians table
     */
    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};
