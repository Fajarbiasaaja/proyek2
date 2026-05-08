<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AddRoleToUsersTable Migration
 * 
 * Menambahkan field 'role' ke tabel users untuk role-based access control (RBAC).
 * Role menentukan apa yang bisa dilakukan user setelah login.
 * 
 * Role Types:
 * - admin: Akses penuh ke dashboard admin, bisa manage semua data
 *   Permissions: View dashboard, manage customers, manage services, manage technicians,
 *               manage bookings, manage invoices, manage sliders, view analytics
 *   
 * - customer: Akses terbatas ke customer dashboard saja
 *   Permissions: View own bookings, view own invoices, can cancel own bookings
 * 
 * Middleware Protection:
 * - Semua admin routes dilindungi dengan middleware 'admin'
 *   (user harus authenticated DAN role='admin')
 * - Semua customer routes dilindungi dengan middleware 'customer'
 *   (user harus authenticated DAN role='customer')
 * 
 * Implementation:
 * - Route::middleware(['auth', 'admin'])->group(...) untuk admin routes
 * - Route::middleware(['auth', 'customer'])->group(...) untuk customer routes
 * - $user->role bisa diakses di controller/view untuk conditional logic
 * 
 * Default Behavior:
 * - Saat user register, role otomatis di-set ke 'customer'
 * - Admin account dibuat manually atau via seeder
 * - Tidak ada UI untuk change role (prevent privilege escalation)
 * 
 * Security Notes:
 * - Role field harus dibaca dari database setiap kali, tidak di-cache di session
 * - Semua admin action harus di-validate di backend (tidak hanya frontend)
 * - Tambah audit log jika ada privilege escalation attempt
 * 
 * Implementasi di Model:
 * @see User model dengan role field casting
 * @see Routes di web.php dengan middleware 'admin' dan 'customer'
 */
return new class extends Migration
{
    /**
     * Run the migrations - add role column to users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Enum field untuk role-based access control
            // Ditempatkan setelah 'email' field
            // Default 'customer' untuk security (least privilege principle)
            $table->enum('role', ['admin', 'customer', 'technician'])->default('customer')->after('email')
                  ->comment('User role for access control: admin (full access), customer (limited access), technician (service provider)');
        });
    }

    /**
     * Reverse the migrations - remove role column
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
