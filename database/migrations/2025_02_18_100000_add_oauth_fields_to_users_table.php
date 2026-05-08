<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AddOAuthFieldsToUsersTable Migration
 * 
 * Menambahkan fields untuk OAuth authentication (social login) ke tabel users.
 * Ini memungkinkan user login via Google, Facebook, GitHub selain email+password.
 * 
 * Fields yang ditambahkan:
 * - provider: Nama OAuth provider (google, facebook, github, dll)
 * - provider_id: Unique ID dari OAuth provider (untuk identify user di provider)
 * - oauth_data: JSON string dengan lengkap data dari OAuth provider
 * 
 * Strategi:
 * 1. User login pertama kali via OAuth:
 *    -> Create user baru dengan provider data
 * 
 * 2. User login lagi via OAuth:
 *    -> Find by provider_id, update oauth_data
 * 
 * 3. User sudah exist (email ada), login via OAuth:
 *    -> Link OAuth ke akun existing
 *    -> Sekarang bisa login via password atau OAuth
 * 
 * Relasi:
 * - provider_id: unique constraint untuk mencegah duplikasi
 * - nullable: fields ini optional (untuk user yang login via email+password)
 * 
 * Implementasi di Model:
 * @see User::findOrCreateFromOAuth()
 */
return new class extends Migration
{
    /**
     * Run the migrations - add OAuth fields to users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // OAuth provider name (google, facebook, github, dll)
            $table->string('provider')
                  ->nullable()
                  ->comment('OAuth provider: google, facebook, github, etc');
            
            // Unique ID dari OAuth provider
            $table->string('provider_id')
                  ->nullable()
                  ->unique() // Unique constraint untuk identify user
                  ->comment('OAuth provider unique user ID');
            
            // Full JSON data dari OAuth provider (untuk reference)
            $table->text('oauth_data')
                  ->nullable()
                  ->comment('JSON data from OAuth provider (name, email, avatar, etc)');
        });
    }

    /**
     * Reverse the migrations - drop OAuth fields
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop semua fields yang ditambah
            $table->dropColumn(['provider', 'provider_id', 'oauth_data']);
        });
    }
};
