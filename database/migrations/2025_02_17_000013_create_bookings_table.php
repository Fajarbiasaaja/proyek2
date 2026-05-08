<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateBookingsTable Migration
 * 
 * Membuat table 'bookings' untuk menyimpan data pemesanan servis AC.
 * Ini adalah tabel central yang menghubungkan customers, services, dan technicians.
 * 
 * Status Workflow:
 * pending -> confirmed -> in_progress -> completed
 * atau cancelled dari status manapun
 * 
 * Table Structure:
 * - id (PK)
 * - customer_id (FK): Refer ke customers table (cascade delete)
 * - service_id (FK): Refer ke services table (cascade delete)
 * - technician_id (FK): Refer ke technicians table (nullable, set null on delete)
 * - scheduled_date: Tanggal & jam jadwal servis
 * - notes: Catatan dari customer tentang masalahnya
 * - status: Status workflow (enum)
 * - total_price: Harga yang disepakati (bisa berbeda dari service.price)
 * - completion_notes: Catatan hasil servis dari technician
 * - timestamps: Audit trail
 * - softDeletes: Archive mode
 * 
 * Relasi:
 * - belongsTo('customer'): Setiap booking milik satu customer
 * - belongsTo('service'): Setiap booking untuk satu service
 * - belongsTo('technician'): Setiap booking ditangani satu technician (nullable)
 * - hasOne('invoice'): Setiap booking bisa punya satu invoice
 * 
 * Cascade Behavior:
 * - customer_id: cascade = jika customer dihapus, bookingnya juga dihapus
 * - service_id: cascade = jika service dihapus, bookingnya juga dihapus
 * - technician_id: set null = jika technician dihapus, field ini jadi NULL
 */
return new class extends Migration
{
    /**
     * Run the migrations - create table
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            
            // Foreign keys
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // CASCADE: hapus booking jika customer dihapus
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // CASCADE: hapus booking jika service dihapus
            $table->foreignId('technician_id')->nullable()->constrained()->onDelete('set null'); // SET NULL: jika technician dihapus, field jadi NULL
            
            // Booking details
            $table->dateTime('scheduled_date'); // Tanggal & jam jadwal servis
            $table->text('notes')->nullable(); // Catatan dari customer (masalah, kebutuhan khusus, dll)
            
            // Status dan pricing
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])
                  ->default('pending'); // Status booking (default: pending)
            $table->decimal('total_price', 10, 2)->nullable(); // Total harga (bisa adjust dari service.price)
            
            // Completion details
            $table->text('completion_notes')->nullable(); // Hasil servis (dari technician)
            
            // Audit trail
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at untuk soft delete
        });
    }

    /**
     * Reverse the migrations - drop table
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
