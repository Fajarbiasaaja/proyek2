<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateUsersTable Migration - Laravel Default Authentication Tables
 * 
 * Membuat 3 tabel core untuk Laravel authentication system:
 * 
 * 1. USERS TABLE
 *    Menyimpan data user account untuk login sekaligus staff/admin.
 *    
 *    Relasi:
 *    - hasOne customer: Setiap user (customer) punya 1 record di customers table
 *    - hasMany bookings (via customer): Customer bisa punya banyak booking
 *    - hasManyThrough invoices (via customer->booking): Customer bisa banyak invoice
 *    
 *    Auth Fields:
 *    - email: Email unik untuk login (also used for password reset)
 *    - password: Hash password (bcrypt) untuk login email+password
 *    - remember_token: Token untuk "remember me" functionality
 *    
 *    Email Verification:
 *    - email_verified_at: Timestamp saat email di-verify
 *    - null = email belum di-verify (optional di app ini)
 *    - Kalau di-implement: Send email with verification link saat register
 *    
 *    OAuth Fields (ditambah oleh migration add_oauth_fields):
 *    - provider: OAuth provider name (google, facebook, github)
 *    - provider_id: Unique ID dari OAuth provider
 *    - oauth_data: JSON full data dari OAuth response
 *    
 *    Role Field (ditambah oleh migration add_role_to_users):
 *    - role: enum('admin', 'customer') untuk RBAC
 *    
 *    Timestamps:
 *    - created_at: Kapan user account dibuat
 *    - updated_at: Kapan user profile terakhir diubah
 * 
 * 2. PASSWORD_RESET_TOKENS TABLE
 *    Menyimpan token untuk reset password flow.
 *    
 *    Use Case:
 *    - User lupa password -> klik "Forgot Password"
 *    - App generate token & kirim via email
 *    - Email contain link dengan token ke reset form
 *    - User masukkan password baru, app verify token
 *    - Token di-delete dari table saat successfully reset
 *    - Token auto-expired setelah X menit (via scheduled job)
 *    
 *    Fields:
 *    - email: Primary key (unique satu reset token per email)
 *    - token: Random token string untuk verification
 *    - created_at: Timestamp pembuatan token
 * 
 * 3. SESSIONS TABLE
 *    Menyimpan session data untuk stateful authentication.
 *    
 *    How It Works:
 *    - Saat user login: Create session record dengan user_id
 *    - Session ID disimpan di browser cookie
 *    - Setiap request: Browser kirim session cookie
 *    - App verify session ID di table, load user data
 *    - Saat logout: Delete session record, clear cookie
 *    
 *    Fields:
 *    - id: Session ID (primary key)
 *    - user_id: Foreign key ke users (null untuk guest)
 *    - ip_address: IP address yang create session (security)
 *    - user_agent: Browser user agent (security)
 *    - payload: Serialized session data (arrays, objects)
 *    - last_activity: Timestamp last request (untuk session timeout)
 *    
 *    Cleanup:
 *    - Old sessions auto-deleted via scheduled job
 *    - Config di config/session.php untuk session lifetime
 * 
 * Implementasi:
 * @see User model untuk user authentication
 * @see Customer model untuk customer profile (hasOne ke users)
 * @see AuthController untuk login/register/logout flow
 * @see config/auth.php untuk auth configuration
 * @see config/session.php untuk session configuration
 */
return new class extends Migration
{
    /**
     * Run the migrations - create authentication tables
     */
    public function up(): void
    {
        // ====== USERS TABLE ======
        // Core authentication table menyimpan user account credentials
        Schema::create('users', function (Blueprint $table) {
            // Primary key - unique identifier untuk user
            $table->id()
                  ->comment('Primary key - unique user ID');
            
            // Name field untuk user/staff profile
            $table->string('name')
                  ->comment('Full name of user/staff member');
            
            // Email unik untuk login dan komunikasi
            $table->string('email')->unique()
                  ->comment('Email address - must be unique (used for login & password reset)');
            
            // Email verification timestamp (nullable = optional feature)
            $table->timestamp('email_verified_at')->nullable()
                  ->comment('Email verification timestamp - null if not verified');
            
            // Password hash (bcrypt) untuk email+password login
            $table->string('password')
                  ->comment('Bcrypt hashed password for email+password authentication');
            
            // Remember-me token untuk persistent login
            $table->rememberToken()
                  ->comment('Token for "remember me" functionality on login form');
            
            // Audit timestamps
            // created_at & updated_at for user account creation/modification tracking
            $table->timestamps();
        });

        // ====== PASSWORD RESET TOKENS TABLE ======
        // Temporary tokens untuk forgot password flow
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Email sebagai primary key (satu reset token per email)
            $table->string('email')->primary()
                  ->comment('Primary key - email address (one reset token per email)');
            
            // Random token untuk verification via email link
            $table->string('token')
                  ->comment('Random token sent in reset email link - verified saat user submit form');
            
            // Audit timestamps
            // created_at & updated_at for password reset token creation tracking
            $table->timestamp('created_at')->nullable();
        });

        // ====== SESSIONS TABLE ======
        // Stateful session storage untuk authentication persistence
        Schema::create('sessions', function (Blueprint $table) {
            // Session ID sebagai primary key (unique identifier)
            $table->string('id')->primary()
                  ->comment('Primary key - session ID (sent in browser cookie)');
            
            // Foreign key ke users table untuk identify logged-in user
            $table->foreignId('user_id')->nullable()->index()
                  ->comment('Foreign key to users - null for guest sessions');
            
            // Client IP address untuk security verification
            $table->string('ip_address', 45)->nullable()
                  ->comment('Client IP address that created session (for security audit)');
            
            // Browser user agent string untuk device identification
            $table->text('user_agent')->nullable()
                  ->comment('Browser user agent string (identify device/browser type)');
            
            // Serialized session data (arrays, objects, values)
            $table->longText('payload')
                  ->comment('Serialized PHP session data (stored in session cookie)');
            
            // Timestamp aktivitas terakhir untuk session timeout
            // Unix timestamp of last activity - used for session timeout calculation
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations - drop authentication tables
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
