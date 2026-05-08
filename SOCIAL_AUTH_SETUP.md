# Social Authentication Setup Guide

## Deskripsi
Sistem login dengan social media provider (Google, Facebook, GitHub) telah diintegrasikan ke dalam aplikasi Anda.

## Tahap Implementasi yang Sudah Dilakukan

✅ **1. Install Laravel Socialite** - Package OAuth untuk Laravel
✅ **2. Database Migration** - Kolom untuk OAuth fields sudah ditambahkan ke tabel users
✅ **3. User Model Update** - Method `findOrCreateFromOAuth()` untuk auto-create user
✅ **4. SocialAuthController** - Controller untuk handle OAuth callback
✅ **5. Routes Configuration** - Route untuk social login sudah ditambahkan
✅ **6. Login View Update** - Tombol social login (Google, Facebook, GitHub) di halaman login
✅ **7. Service Config** - Konfigurasi services.php untuk OAuth providers

---

## Langkah Selanjutnya: Setup Credentials

### 📋 Prasyarat
1. Pastikan MySQL sudah berjalan (buka XAMPP Control Panel, start MySQL)
2. Database `proyek2_ac` sudah dibuat dan konfigurasi .env sudah benar

### 1️⃣ Setup Google OAuth

#### Step 1: Buat Google OAuth Credentials
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru (atau gunakan yang ada)
3. Aktifkan **Google+ API**
4. Buat **OAuth 2.0 Credential**:
   - Apps type: Web application
   - Authorized JavaScript origins: `http://localhost:8000`
   - Authorized redirect URIs: `http://localhost:8000/login/google/callback`
5. Copy **Client ID** dan **Client Secret**

#### Step 2: Update .env
```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/login/google/callback
```

---

### 2️⃣ Setup Facebook OAuth

#### Step 1: Buat Facebook App
1. Buka [Facebook Developers](https://developers.facebook.com/)
2. Buat App baru dan pilih tipe "Consumer"
3. Gunakan product **Facebook Login**
4. Di Settings > Basic, copy **App ID** dan **App Secret**
5. Di Facebook Login > Settings:
   - Valid OAuth Redirect URIs: `http://localhost:8000/login/facebook/callback`
   - Deauthorize Callback URL: `http://localhost:8000/login/facebook/callback`

#### Step 2: Update .env
```env
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/login/facebook/callback
```

---

### 3️⃣ Setup GitHub OAuth

#### Step 1: Buat GitHub OAuth App
1. Buka [GitHub Settings > Developer settings > OAuth Apps](https://github.com/settings/developers)
2. Klik "New OAuth App"
3. Isi form:
   - Application name: `Jasa Servis AC`
   - Homepage URL: `http://localhost:8000`
   - Authorization callback URL: `http://localhost:8000/login/github/callback`
4. Copy **Client ID** dan generate **Client Secret**

#### Step 2: Update .env
```env
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URI=http://localhost:8000/login/github/callback
```

---

## 🗄️ Jalankan Database Migration

Setelah konfigurasi .env selesai, jalankan migration untuk menambahkan kolom OAuth:

```bash
php artisan migrate
```

Ini akan menambahkan 3 kolom ke tabel `users`:
- `provider` - Nama OAuth provider (google, facebook, github)
- `provider_id` - User ID dari OAuth provider
- `oauth_data` - JSON data dari OAuth provider

---

## 🧪 Testing

### Test Google Login
1. Jalankan aplikasi: `php artisan serve`
2. Kunjungi halaman login: `http://localhost:8000/login`
3. Klik tombol Google
4. Login dengan akun Google Anda

### Test Facebook Login
1. Klik tombol Facebook di halaman login
2. Login dengan akun Facebook Anda

### Test GitHub Login
1. Klik tombol GitHub di halaman login
2. Login dengan akun GitHub Anda

---

## 📁 Files yang Diubah/Ditambahkan

### Ditambahkan:
- `app/Http/Controllers/SocialAuthController.php` - Controller untuk social auth
- `database/migrations/2025_02_18_100000_add_oauth_fields_to_users_table.php` - Migration untuk OAuth fields

### Diupdate:
- `composer.json` - Laravel Socialite package ditambahkan
- `app/Models/User.php` - Method baru `findOrCreateFromOAuth()`
- `routes/web.php` - Social authentication routes
- `resources/views/auth/login.blade.php` - Social login buttons
- `config/services.php` - OAuth providers configuration
- `.env.example` - OAuth environment variables template

---

## 🔧 Fitur-Fitur

### Auto User Registration
- User login pertama kali dengan OAuth akan otomatis dibuat di database
- Email dari OAuth provider akan digunakan sebagai email user

### Link to Existing Account
- Jika email sudah terdaftar, user yang ada akan di-link dengan OAuth provider
- Ini memungkinkan user untuk login dengan cara berbeda (email+password atau OAuth)

### Customer Record Auto-Create
- Saat user baru registrasi via OAuth, record Customer juga otomatis dibuat

### Redirect ke Dashboard
- Admin akan di-redirect ke admin dashboard
- Customer akan di-redirect ke customer dashboard

---

## ⚠️ Troubleshooting

### Error: "Provider tidak didukung"
- Pastikan hanya menggunakan provider: google, facebook, atau github

### Error: "Gagal menghubungkan ke [Provider]"
- Cek credentials di .env sudah benar
- Pastikan URL di OAuth app configuration sesuai dengan APP_URL di .env
- Untuk development: gunakan `http://localhost:8000`

### User tidak bisa login dengan OAuth
- Cek permissionnya di OAuth app settings
- Database belum di-migrate
- Credentials di .env belum diisi

### Social buttons tidak muncul
- Clear browser cache
- Jalankan: `php artisan config:cache`

---

## 📚 Referensi

- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
- [Google OAuth Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Facebook Login Documentation](https://developers.facebook.com/docs/facebook-login)
- [GitHub OAuth Documentation](https://docs.github.com/en/developers/apps/building-oauth-apps)

---

## 💡 Tips & Best Practices

1. **Jangan commit credentials** - .env harus selalu di-.gitignore
2. **Use HTTPS di production** - OAuth hanya bekerja dengan HTTPS di production
3. **Secure credentials** - Jangan bagikan Client Secret ke orang lain
4. **Test thoroughly** - Test setiap provider sebelum go live
5. **Keep secrets hidden** - Environment variables adalah best practice

---

## 🔐 Security Notes

- Provider IDs disimpan unique di database untuk mencegah duplikasi
- Passwords OAuth users adalah random untuk security
- OAuth data disimpan dalam JSON format di kolom oauth_data
- Semua OAuth action di-log untuk audit trail

---

**Selamat! Sistem Social Authentication Anda sudah siap untuk dikonfigurasi! 🎉**
