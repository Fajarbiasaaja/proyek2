# 🔐 SISTEM LOGIN APLIKASI JASA SERVIS AC

## 📋 Daftar Isi
1. [Fitur Login](#fitur-login)
2. [Akun Test](#akun-test)
3. [Struktur Database](#struktur-database)
4. [Cara Kerja](#cara-kerja)
5. [Role & Permission](#role--permission)

---

## 🔑 Fitur Login

Aplikasi Jasa Servis AC dilengkapi dengan sistem autentikasi 2 role:

### 1. **Admin**
- Akses penuh ke seluruh menu aplikasi
- Manage pelanggan, teknisi, layanan
- Manage pemesanan dan invoice
- Melihat dashboard statistik

### 2. **Pelanggan**
- Dashboard khusus pelanggan
- Melihat riwayat pemesanan
- Tracking status pemesanan
- Melihat invoice dan history pembayaran
- Registrasi akun sendiri

---

## 👤 Akun Test

### Admin Account
```
Email: admin@example.com
Password: password
Role: Admin
```

### Customer Account (Test 1)
```
Email: customer@example.com
Password: password
Role: Customer
```

### Customer Account (Test 2)
```
Email: siti.customer@example.com
Password: password
Role: Customer
```

---

## 🗄️ Struktur Database

### Migrasi yang Ditambahkan:
- **Migration**: `2025_02_17_000015_add_role_to_users_table.php`
  - Menambahkan kolom `role` (enum: admin, customer) ke tabel `users`

### Tabel Users Structure:
```sql
- id (bigint, primary key)
- name (varchar)
- email (varchar, unique)
- email_verified_at (timestamp, nullable)
- password (varchar)
- role (enum: 'admin', 'customer') -- BARU
- remember_token (varchar, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## 🔄 Cara Kerja

### 1. **Flow Login Admin**
```
(1) User mengakses /login
↓
(2) Masukkan email admin@example.com & password
↓
(3) Sistem cek credentials & role
↓
(4) Jika role = admin → Redirect ke /dashboard (Admin Dashboard)
↓
(5) Akses penuh ke fitur administratif
```

### 2. **Flow Login Pelanggan**
```
(1) User mengakses /login atau /register
↓
(2) Login: Masukkan email & password yang sudah terdaftar
   ATAU Register: Isi form daftar akun baru
↓
(3) Sistem cek credentials
↓
(4) Jika role = customer → Redirect ke /customer/dashboard
↓
(5) Akses dashboard pelanggan
```

### 3. **Flow Register Pelanggan Baru**
```
(1) Klik "Daftar Sebagai Pelanggan" di halaman login
↓
(2) Isi form:
    - Nama Lengkap
    - Email
    - Nomor Telepon
    - Alamat
    - Password (min 8 karakter)
    - Konfirmasi Password
↓
(3) Submit form
↓
(4) Sistem membuat:
    - User account dengan role='customer'
    - Customer record otomatis
↓
(5) Auto login & redirect ke /customer/dashboard
```

---

## 👥 Role & Permission

### Middleware Protection
```php
// Admin Routes - Protected by 'admin' middleware
Route::middleware(['auth', 'admin'])->group(function () {
    // Akses admin dashboard
    Route::get('/dashboard', ...);
    // CRUD customers, technicians, services
    Route::resource('customers', ...);
    // Dan lainnya
});

// Customer Routes - Protected by 'customer' middleware
Route::middleware(['auth', 'customer'])->group(function () {
    // Akses customer dashboard
    Route::get('/customer/dashboard', ...);
});
```

### Middleware Classes
1. **CheckAdminRole** (`app/Http/Middleware/CheckAdminRole.php`)
   - Validasi user adalah admin
   - Jika bukan → abort 403

2. **CheckCustomerRole** (`app/Http/Middleware/CheckCustomerRole.php`)
   - Validasi user adalah customer
   - Jika bukan → abort 403

---

## 🎯 URL Penting

| URL | Deskripsi |
|-----|-----------|
| `/` | Home page |
| `/login` | Login page |
| `/register` | Register page pelanggan |
| `/logout` | Logout user |
| `/dashboard` | Admin dashboard (auth + admin) |
| `/customer/dashboard` | Customer dashboard (auth + customer) |
| `/customers` | CRUD customers (admin only) |
| `/bookings` | Booking management (admin only) |
| `/invoices` | Invoice management (admin only) |

---

## 📁 File-File Baru

### Controllers:
- `app/Http/Controllers/AuthController.php` - Handle login/register/logout
- `app/Http/Controllers/CustomerDashboardController.php` - Customer dashboard

### Middleware:
- `app/Http/Middleware/CheckAdminRole.php`
- `app/Http/Middleware/CheckCustomerRole.php`

### Views/Blade:
- `resources/views/auth/login.blade.php` - Login page
- `resources/views/auth/register.blade.php` - Register page
- `resources/views/customer/dashboard.blade.php` - Customer dashboard

### Seeder:
- `database/seeders/UserSeeder.php` - Create test users

### Migrations:
- `database/migrations/2025_02_17_000015_add_role_to_users_table.php`

---

## 🔒 Session Management

- Session otomatis di-regenerate setelah login
- Session di-invalidate saat logout
- Token CSRF untuk keamanan form submission
- Password di-hash menggunakan bcrypt

---

## 🎨 UI/UX

### Login Page
- Design modern dengan gradient background
- Form validation display
- Demo credential hint
- Button daftar akun baru

### Register Page
- Form lengkap dengan validasi
- Minimum password 8 karakter
- Password confirmation
- Link back to login

### User Menu (Navbar)
- Dropdown user menu di navbar kanan
- Tampil nama user & role badge
- Quick logout button
- Email terlihat di dropdown header

---

## ⚠️ Important Notes

1. **Email Unique**: Setiap email hanya bisa digunakan 1x
2. **Password Minimum 8**: Register password minimal 8 karakter
3. **Redirect Logic**: 
   - Admin login → `/dashboard`
   - Customer login → `/customer/dashboard`
   - Guest → `/login`
4. **Customer Auto Account**: Saat registrasi, akan otomatis membuat customer record
5. **Session Security**: 
   - Session di-regenerate tiap login
   - CSRF token di-check di setiap form

---

## 🚀 Panduan Penggunaan

### Untuk Admin:
```
1. Akses http://localhost:8000/login
2. Email: admin@example.com
3. Password: password
4. Akan langsung ke admin dashboard
5. Kelola semua hal dari sidebar menu
```

### Untuk Customer:
```
1. Opsi A - Login:
   - Akses http://localhost:8000/login
   - Email: customer@example.com / siti.customer@example.com
   - Password: password
   
2. Opsi B - Register Baru:
   - Klik "Daftar Sebagai Pelanggan"
   - Isi semua field
   - Akan otomatis login & ke dashboard
```

---

## 📝 Catatan Pengembangan

- Database sudah include migrasi dan seeder
- Semua file sudah terbuat dan terintegrasi
- Middleware sudah terdaftar di bootstrap/app.php
- Routes sudah dikonfigurasi dengan middleware
- Ready untuk production (perlu konfigurasi email production)

---

**Last Update**: February 17, 2026
**Status**: ✅ Complete & Ready to Use
