# 📱 APLIKASI JASA SERVIS AC - DENGAN SISTEM LOGIN

Aplikasi manajemen jasa servis AC yang dibangun dengan Laravel 11 dan Bootstrap 5, dilengkapi dengan sistem autentikasi 2 role (Admin & Customer).

## ✨ Fitur Terbaru

### 🔐 **Sistem Login Lengkap**
- ✅ Login dengan email & password
- ✅ Register akun pelanggan baru
- ✅ 2 Role: Admin & Customer
- ✅ Session management
- ✅ Logout functionality

---

## 🎯 Fitur Utama

### Admin Features:
- **Dashboard** - Ringkasan statistik & KPI
- **Manajemen Pelanggan** - CRUD pelanggan
- **Manajemen Teknisi** - Kelola teknisi & status
- **Manajemen Layanan** - CRUD layanan AC
- **Manajemen Pemesanan** - Handle booking dan status
- **Manajemen Invoice** - Tracking pembayaran

### Customer Features:
- **Dashboard Pelanggan** - Overview pemesanan & invoice
- **Riwayat Pemesanan** - Tracking booking status
- **Invoice History** - Lihat & tracking pembayaran
- **Profile** - Kelola data customer
- **Available Services** - Browse layanan yang tersedia

---

## 👤 Akun Test Default

### Admin
```
Email: admin@example.com
Password: password
```

### Customer (Test Account 1)
```
Email: customer@example.com
Password: password
```

### Customer (Test Account 2)
```
Email: siti.customer@example.com
Password: password
```

---

## 🚀 Quick Start

### 1. Install & Setup
```bash
cd c:\xampp\htdocs\proyek2

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrasi & seeding
php artisan migrate:fresh --seed

# Build assets
npm run build

# Jalankan server
php artisan serve
```

### 2. Akses Aplikasi
- **Home**: http://localhost:8000
- **Login**: http://localhost:8000/login
- **Admin Dashboard**: http://localhost:8000/dashboard
- **Customer Dashboard**: http://localhost:8000/customer/dashboard

---

## 📁 Struktur Project

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php ⭐ Login/Register/Logout
│   │   ├── CustomerDashboardController.php ⭐ Customer Dashboard
│   │   ├── DashboardController.php (Admin)
│   │   └── ... (Controllers lainnya)
│   └── Middleware/
│       ├── CheckAdminRole.php ⭐ Admin validator
│       └── CheckCustomerRole.php ⭐ Customer validator
├── Models/
│   ├── User.php (Updated with 'role')
│   ├── Customer.php
│   ├── Technician.php
│   ├── Service.php
│   ├── Booking.php
│   └── Invoice.php

database/
├── migrations/
│   ├── ...
│   └── 2025_02_17_000015_add_role_to_users_table.php ⭐
├── seeders/
│   ├── UserSeeder.php ⭐ Admin & Customer test users
│   ├── CustomerSeeder.php
│   ├── TechnicianSeeder.php
│   ├── ServiceSeeder.php
│   └── DatabaseSeeder.php

resources/
└── views/
    ├── auth/
    │   ├── login.blade.php ⭐
    │   └── register.blade.php ⭐
    ├── customer/
    │   └── dashboard.blade.php ⭐
    ├── layouts/
    │   └── app.blade.php (Updated)
    ├── dashboard.blade.php
    ├── customers/
    ├── technicians/
    ├── services/
    ├── bookings/
    └── invoices/

routes/
└── web.php (Updated with auth routes & middleware)

bootstrap/
└── app.php (Updated with middleware aliases)
```

⭐ = File/Features Login Baru

---

## 🔐 Security Features

- ✅ CSRF protection on forms
- ✅ Password hashing (bcrypt)
- ✅ Session regeneration after login
- ✅ Role-based access control
- ✅ Middleware validation
- ✅ Email uniqueness validation

---

## 📊 Flow Diagram

### Login Process
```
User → /login → Validate Credentials → Check Role
  ↓
  Admin Role → /dashboard (Admin Area)
  Customer Role → /customer/dashboard (Customer Area)
```

### Register Process
```
User → /register → Fill Form → Validate Data
  ↓
  Create User Account (role='customer')
  Auto Create Customer Record
  Auto Login → /customer/dashboard
```

### Logout Process
```
User → Click Logout → Invalidate Session
  ↓
  Redirect to /login
```

---

## 🎨 UI Components

### Authentication Pages
- **Login Page**: Modern design dengan form validation
- **Register Page**: Complete form dengan password confirmation
- **Navbar**: User dropdown menu dengan logout option

### Dashboard Pages
- **Admin Dashboard**: Stats cards, recent bookings, service list
- **Customer Dashboard**: Personal stats, recent bookings, unpaid invoices

---

## 🛠️ Technology Stack

| Komponen | Technology |
|----------|-----------|
| Backend | Laravel 11 |
| Database | SQLite (atau MySQL) |
| Frontend | Blade Template |
| CSS Framework | Bootstrap 5 |
| Icons | Bootstrap Icons |
| Validation | Laravel Validation |
| Authentication | Laravel Auth (Built-in) |

---

## 📱 Features Summary

### Total Fitur: 15+

#### Authentication (4 fitur):
- Login
- Register
- Logout
- Role-based routing

#### Admin Panel (11 fitur):
- Dashboard dengan statistik
- CRUD Customers
- CRUD Technicians
- CRUD Services
- CRUD Bookings
- CRUD Invoices
- Invoice billing otomatis
- Status tracking
- Data reports

#### Customer Panel (7 fitur):
- Dashboard dengan personal stats
- View booking history
- Track booking status
- View invoices
- Track payment status
- Browse services
- Personal profile

---

## 📖 Documentation Files

1. **LOGIN_DOCUMENTATION.md** - Dokumentasi lengkap login system
2. **APLIKASI_AC_README.md** - Dokumentasi awal aplikasi
3. **README.md** - File ini

---

## ✅ Checklist Implementation

- [x] Database migrasi untuk role
- [x] Middleware untuk role validation
- [x] AuthController untuk login/register
- [x] Login & Register views
- [x] Customer Dashboard
- [x] Route protection dengan middleware
- [x] User menu di navbar
- [x] Logout functionality
- [x] Seeder dengan test users
- [x] Customer auto-record creation
- [x] Session management
- [x] CSRF protection

---

## 🔄 Workflow Aplikasi

### Admin Workflow:
```
Login (admin@example.com)
  ↓
Dashboard (Lihat statistik)
  ↓
Manage Customers/Technicians/Services
  ↓
Manage Bookings (accept, assign teknisi, etc)
  ↓
Generate & Manage Invoices
  ↓
Logout
```

### Customer Workflow:
```
Register / Login
  ↓
Dashboard (Lihat status pemesanan)
  ↓
View Services / Make Booking (coming soon)
  ↓
Track Booking Status
  ↓
View Invoice & Pay (coming soon)
  ↓
Logout
```

---

## 🚦 Status Routes

| Route | Role | Status |
|-------|------|--------|
| /login | Guest | ✅ Active |
| /register | Guest | ✅ Active |
| /logout | Auth | ✅ Active |
| /dashboard | Admin | ✅ Active |
| /customer/dashboard | Customer | ✅ Active |
| /customers (CRUD) | Admin | ✅ Active |
| /technicians (CRUD) | Admin | ✅ Active |
| /services (CRUD) | Admin | ✅ Active |
| /bookings (CRUD) | Admin | ✅ Active |
| /invoices | Admin | ✅ Active |

---

## 🎓 Cara Menggunakan

### Sebagai Admin:
1. Buka http://localhost:8000/login
2. Masuk dengan `admin@example.com` / `password`
3. Akan langsung ke dashboard admin
4. Kelola semua fitur dari sidebar

### Sebagai Customer:
1. **Jika sudah terdaftar:**
   - Buka http://localhost:8000/login
   - Masuk dengan email & password customer
   - Akan ke customer dashboard

2. **Jika belum terdaftar:**
   - Buka http://localhost:8000/register
   - Isi form registrasi lengkap
   - Akan auto login ke customer dashboard

---

## ⚠️ Important Notes

1. **Password Security**: Password di-hash dengan bcrypt
2. **Email Unique**: Tidak boleh ada email yang sama
3. **Register Validation**: All fields required
4. **Password Minimum**: 8 characters
5. **Auto Customer Record**: Saat customer register, customer record otomatis dibuat

---

## 🐛 Troubleshooting

### Login tidak bisa masuk
- Pastikan email & password benar
- Pastikan user sudah terdaftar di database
- Check browser cookies/cache

### Session error
- Clear browser cache
- Atau gunakan incognito/private window

### Register failed
- Pastikan email belum terdaftar
- Pastikan password minimal 8 karakter
- Pastikan semua field terisi

---

## 📞 Support & Contact

Untuk question atau bug report, silakan hubungi developer.

---

## 📅 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.1 | Feb 17, 2026 | ✅ Added Authentication System |
| 1.0 | Feb 17, 2026 | Initial Release |

---

**Status**: ✅ Production Ready  
**Last Updated**: February 17, 2026  
**Platform**: Laravel 11 + Bootstrap 5  
**License**: Open Source

---

## 🎉 Terimakasih!

Aplikasi Jasa Servis AC sekarang sudah dilengkapi dengan sistem login yang lengkap dan aman. Semoga bermanfaat!

Happy Coding! 🚀
