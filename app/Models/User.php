<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 * 
 * Model untuk mengelola data pengguna sistem (admin, customer, technician).
 * Mendukung autentikasi tradisional (email/password) dan OAuth (Google, Facebook, GitHub).
 * 
 * Status Roles:
 * - admin: Pengguna admin sistem
 * - customer: Pelanggan yang melakukan booking
 * - technician: Teknisi yang menangani servis
 * 
 * @property int $id
 * @property string $name Nama pengguna
 * @property string $email Email pengguna (unique)
 * @property string $password Password (hashed)
 * @property string $role Role pengguna (admin/customer/technician)
 * @property string|null $provider OAuth provider (google/facebook/github)
 * @property string|null $provider_id OAuth provider user ID
 * @property array|null $oauth_data JSON data dari OAuth provider
 * @property string|null $remember_token Token untuk remember me
 * @property timestamp $email_verified_at
 * @property timestamp $created_at
 * @property timestamp $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat di-assign secara massal (mass assignable)
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'provider',
        'provider_id',
        'oauth_data',
    ];

    /**
     * Atribut yang harus disembunyikan saat serialization (JSON response)
     * Password dan remember_token tidak boleh exposed ke client
     * 
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting untuk atribut tertentu
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'oauth_data' => 'json', // Casting ke JSON array
        ];
    }

    /**
     * Relasi: User hasOne Technician
     * Jika user memiliki role='technician', maka ada one technician record
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function technician()
    {
        return $this->hasOne(Technician::class);
    }

    /**
     * Temukan atau buat user dari OAuth provider
     * 
     * Strategi:
     * 1. Cari by provider_id (sudah login sebelumnya)
     * 2. Cari by email (user sudah ada, link OAuth)
     * 3. Create user baru (first time login)
     * 
     * @param string $provider Nama provider (google/facebook/github)
     * @param object $oauthUser Object dari Socialite dengan data user
     * @return User User yang sudah ada atau baru dibuat
     */
    public static function findOrCreateFromOAuth(string $provider, $oauthUser): self
    {
        // Step 1: Cek by provider_id untuk update data OAuth yang existing
        $user = static::where('provider_id', $oauthUser->getId())->first();
        
        if ($user) {
            // User sudah login via OAuth sebelumnya, update data
            $user->update([
                'provider' => $provider,
                'oauth_data' => json_encode($oauthUser),
            ]);
            return $user;
        }

        // Step 2: Cek by email untuk link OAuth ke akun existing
        $user = static::where('email', $oauthUser->getEmail())->first();
        
        if ($user) {
            // User sudah ada, link dengan OAuth provider ini
            // Ini memungkinkan user login via email+password atau OAuth
            $user->update([
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
                'oauth_data' => json_encode($oauthUser),
            ]);
            return $user;
        }

        // Step 3: Buat user baru dari OAuth data
        return static::create([
            'name' => $oauthUser->getName() ?? $oauthUser->getNickname(),
            'email' => $oauthUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $oauthUser->getId(),
            'oauth_data' => json_encode($oauthUser),
            'role' => 'customer', // Default role untuk OAuth user adalah customer
            'password' => bcrypt('oauth_' . uniqid()), // Password random untuk security
        ]);
    }
}
