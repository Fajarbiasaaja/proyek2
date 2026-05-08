<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateCustomersTable Migration
 * 
 * Membuat table 'customers' untuk menyimpan data pelanggan yang terdaftar.
 * 
 * Table Structure:
 * - id (PK)
 * - name: Nama lengkap pelanggan
 * - phone: Nomor telepon/HP untuk kontak
 * - email: Email pelanggan (unique - satu email hanya satu customer)
 * - address: Alamat lengkap tempat tinggal
 * - city: Kota (optional)
 * - postal_code: Kode pos (optional)
 * - timestamps: created_at, updated_at untuk audit trail
 * - softDeletes: deleted_at untuk soft delete (archive mode)
 * 
 * Relasi:
 * - hasMany('bookings'): Customer bisa punya banyak bookings
 * - hasManyThrough('invoices'): Customer punya invoices via bookings
 * 
 * Import Notes:
 * - Email harus unique untuk mencegah duplikasi
 * - Soft deletes digunakan untuk keep data di database (archive)
 * - Tidak ada direct FK ke users (separate data structure)
 */
return new class extends Migration
{
    /**
     * Run the migrations - create table
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('name'); // Nama pelanggan
            $table->string('phone'); // Nomor telepon
            $table->string('email')->unique(); // Email (unique constraint)
            $table->text('address'); // Alamat lengkap
            $table->string('city')->nullable(); // Kota (optional)
            $table->string('postal_code')->nullable(); // Kode pos (optional)
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at untuk soft delete
        });
    }

    /**
     * Reverse the migrations - drop table
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
