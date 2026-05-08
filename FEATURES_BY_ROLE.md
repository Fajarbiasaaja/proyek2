# 📋 Fitur JasaKu - Berdasarkan Role dan Use Case

## Overview Sistem
Aplikasi JasaKu adalah platform booking layanan dengan 3 role utama:
1. **CUSTOMER (Pelanggan)** - Pemohon jasa
2. **TECHNICIAN (Penyedia Jasa)** - Penyedia layanan/teknisi
3. **ADMIN** - Administrator sistem

---

## 🛑 ROLE 1: CUSTOMER (Pelanggan / User)

### 1.1 Autentikasi & Pendaftaran
**Use Case: Registrasi Akun**
- ✅ Registrasi dengan email dan password
- ✅ Login dengan email/password
- ✅ Login dengan OAuth (Google, Facebook, GitHub)
- ✅ Logout
- ✅ Lupa password

**Routes:**
```
POST /register             - Customer self-register
GET  /login                - Show login form
POST /login                - Process login
POST /logout               - Logout user
GET  /login/{provider}     - OAuth redirect (google|facebook|github)
GET  /login/{provider}/callback - OAuth callback
```

**Controllers:** `AuthController`, `SocialAuthController`

---

### 1.2 Manajemen Profil
**Use Case: Mengelola Data Diri**
- ✅ Lihat profil
- ✅ Edit nama dan informasi pribadi
- ✅ Edit email
- ✅ Ubah password

**Routes:**
```
GET  /profile/edit-email        - Show email edit form
PUT  /profile/update-email      - Update email
GET  /profile/edit-password     - Show password edit form
PUT  /profile/update-password   - Update password
GET  /profile/edit              - Show profile edit form
PUT  /profile/update            - Update profile
```

**Controllers:** `ProfileController`

---

### 1.3 Jelajahi Layanan
**Use Case: Melihat Daftar Jasa**
- ✅ Lihat semua layanan yang tersedia
- ✅ Filter layanan berdasarkan kategori
- ✅ Cari layanan berdasarkan nama

**Use Case: Melihat Detail Jasa**
- ✅ Lihat detail layanan
- ✅ Lihat harga
- ✅ Lihat deskripsi lengkap
- ✅ Lihat rating teknisi yang bisa handle layanan ini

**Routes:**
```
GET  /customer/services         - List all services (paginated, searchable, filterable)
```

**Controllers:** `ServiceController`

---

### 1.4 Membuat & Mengelola Pemesanan
**Use Case: Memesan Jasa**
- ✅ Pilih layanan
- ✅ Tentukan tanggal dan waktu
- ✅ Tambahkan catatan/deskripsi lokasi
- ✅ Lihat total harga
- ✅ Lakukan pemesanan

**Use Case: Melihat Riwayat Pesanan**
- ✅ Lihat semua pesanan customer
- ✅ Filter berdasarkan status (pending, confirmed, in_progress, completed, cancelled)
- ✅ Lihat detail pesanan
- ✅ Edit pesanan (status pending)
- ✅ Batalkan pesanan

**Routes:**
```
GET  /customer/bookings              - List customer bookings (paginated, filterable)
GET  /customer/bookings/create       - Show booking creation form
POST /customer/bookings              - Create new booking
GET  /customer/bookings/{booking}    - Show booking detail
GET  /customer/bookings/{booking}/edit - Show edit form
PUT  /customer/bookings/{booking}    - Update booking
DELETE /customer/bookings/{booking}  - Delete booking
POST /customer/bookings/{booking}/cancel - Cancel booking
```

**Status Workflow:**
- `pending` → `confirmed` → `in_progress` → `completed`
- Any status → `cancelled`

**Controllers:** `CustomerBookingController`

---

### 1.5 Invoice & Pembayaran
**Use Case: Melakukan Pembayaran**
- ✅ Lihat invoice dari booking yang selesai
- ✅ Lihat rincian invoice (item, harga, pajak, total)
- ✅ Submit pembayaran dengan berbagai metode
- ✅ Lihat status pembayaran

**Use Case: Melihat Riwayat Pembayaran**
- ✅ Lihat semua invoice (paginated)
- ✅ Lihat detail invoice
- ✅ Track status pembayaran
- ✅ Lihat payment history per invoice
- ✅ Cancel pembayaran jika masih pending

**Routes:**
```
GET  /customer/invoices                         - List customer invoices
GET  /customer/invoices/{invoice}               - Show invoice detail
GET  /customer/invoices/{invoice}/payment-form - Show payment form
POST /customer/invoices/{invoice}/payment-submit - Submit payment
GET  /customer/invoices/{invoice}/payment-history - View payment history for invoice
DELETE /payments/{payment}/cancel               - Cancel pending payment
GET  /api/payments/{payment}/status             - Check payment status (API)
```

**Payment Status:** `pending` → `approved` → `confirmed`

**Controllers:** `PaymentController`, `InvoiceController`

---

### 1.6 Rating & Review
**Use Case: Memberi Ulasan/Rating**
- ✅ Lihat rating untuk booking yang completed
- ✅ Beri rating (1-5 bintang) ke technician
- ✅ Tulis review/komentar
- ✅ Edit rating & review
- ✅ Hapus rating & review

**Routes:**
```
GET  /customer/bookings/{booking}/ratings  - Show rating form for completed booking
POST /customer/bookings/{booking}/ratings  - Store rating
PUT  /ratings/{rating}                     - Update rating
DELETE /ratings/{rating}                   - Delete rating
```

**Constraints:**
- Hanya bisa rating untuk booking dengan status `completed`
- Satu booking hanya bisa punya satu rating
- Hanya pemberi rating yang bisa edit/delete

**Controllers:** `RatingController`

---

### 1.7 Dashboard Customer
**Use Case: Dashboard & Overview**
- ✅ Statistik: Total bookings, Completed, Pending, Cancelled
- ✅ Upcoming bookings (dalam 7 hari)
- ✅ Recent payments
- ✅ Top-rated technicians recommendation

**Routes:**
```
GET  /customer/dashboard  - Customer dashboard
```

**Controllers:** `CustomerDashboardController`

---

## 🔧 ROLE 2: TECHNICIAN (Penyedia Jasa / Teknisi)

### 2.1 Autentikasi & Pendaftaran
**Use Case: Registrasi Akun Penyedia Jasa**
- ✅ Registrasi sebagai penyedia jasa (technician)
- ✅ Login sebagai technician
- ✅ Logout

**Routes:**
```
GET  /register/provider           - Show provider registration form
POST /register/provider           - Register as provider
GET  /login/technician            - Show technician login
POST /login/technician            - Process technician login
POST /logout                       - Logout
```

**Controllers:** `AuthController`

---

### 2.2 Manajemen Profil
**Use Case: Mengelola Profil**
- ✅ Lihat profil sebagai technician
- ✅ Edit informasi profil
- ✅ Edit spesialisasi/keahlian
- ✅ Edit foto profil
- ✅ Edit area layanan

**Routes:**
```
GET  /profile/edit        - Show profile edit form
PUT  /profile/update      - Update profile
GET  /profile/edit-email  - Show email edit form
PUT  /profile/update-email - Update email
GET  /profile/edit-password - Show password edit form
PUT  /profile/update-password - Update password
```

**Controllers:** `ProfileController`

---

### 2.3 Kelola Pemesanan
**Use Case: Melihat & Mengelola Pesanan**
- ✅ Lihat semua pesanan yang di-assign ke technician
- ✅ Filter berdasarkan status
- ✅ Lihat detail pesanan
- ✅ Terima/confirm pesanan (jika ada booking assignment flow)
- ✅ Mulai pekerjaan (ubah status ke in_progress)
- ✅ Tandai selesai (ubah status ke completed)
- ✅ Tambahkan catatan penyelesaian

**Routes:**
```
GET  /technician/dashboard              - Technician dashboard
GET  /technician/bookings               - List assigned bookings
GET  /technician/bookings/{booking}     - Show booking detail
POST /technician/bookings/{booking}/mark-completed - Mark as completed
```

**Booking Statuses untuk Technician:**
- `pending` - Baru di-assign, belum dikerjakan
- `in_progress` - Sedang dikerjakan
- `completed` - Selesai
- `cancelled` - Dibatalkan

**Controllers:** `TechnicianDashboardController`, `CustomerBookingController`, `BookingController`

---

### 2.4 Dashboard & Statistik Kerja
**Use Case: Lihat Riwayat Pesanan & Earnings**
- ✅ Statistik: Total bookings, Completed, Active, Rating rata-rata
- ✅ Lihat upcoming bookings
- ✅ Lihat completed bookings dengan rating
- ✅ Track earnings/income
- ✅ View performance metrics

**Routes:**
```
GET  /technician/dashboard  - Technician dashboard dengan statistics
```

**Controllers:** `TechnicianDashboardController`

---

### 2.5 Rating & Review (View Only)
**Use Case: Lihat Rating Diri Sendiri**
- ✅ Lihat semua rating yang diterima
- ✅ Lihat review dari customers
- ✅ Lihat average rating
- ✅ Public profile dengan rating

**Routes:**
```
GET  /technicians/{technician}/ratings  - Get ratings for technician (public)
```

**Controllers:** `RatingController`

---

## 👨‍💼 ROLE 3: ADMIN

### 3.1 Autentikasi
**Use Case: Login Admin**
- ✅ Login dengan email/password
- ✅ Logout
- ✅ Role-based access control

**Routes:**
```
GET  /login        - Admin login (shared dengan customer)
POST /login        - Process login
POST /logout       - Logout
```

**Controllers:** `AuthController`

---

### 3.2 Dashboard Admin
**Use Case: Dashboard & Overview**
- ✅ Statistik utama: Total users, Total bookings, Total revenue, Pending payments
- ✅ Recent bookings
- ✅ Recent payments
- ✅ System health/statistics

**Routes:**
```
GET  /dashboard  - Admin dashboard dengan comprehensive statistics
```

**Controllers:** `DashboardController`

---

### 3.3 Manajemen Data Customer
**Use Case: Mengelola Data User**
- ✅ List semua customers (paginated, searchable)
- ✅ Lihat detail customer
- ✅ Edit data customer
- ✅ Delete customer (soft delete)
- ✅ Filter berdasarkan status/kriteria

**Routes:**
```
GET    /customers              - List customers (CRUD index)
GET    /customers/create       - Show create form
POST   /customers              - Store new customer
GET    /customers/{customer}   - Show customer detail
GET    /customers/{customer}/edit - Show edit form
PUT    /customers/{customer}   - Update customer
DELETE /customers/{customer}   - Delete customer
```

**Controllers:** `CustomerController`

---

### 3.4 Manajemen Penyedia Jasa (Technician)
**Use Case: Mengelola Penyedia Jasa**
- ✅ List semua technicians (paginated, searchable)
- ✅ Lihat detail technician
- ✅ Edit data technician
- ✅ Delete technician (soft delete)
- ✅ View technician ratings & performance

**Routes:**
```
GET    /technicians                  - List technicians (CRUD index)
GET    /technicians/create           - Show create form
POST   /technicians                  - Store new technician
GET    /technicians/{technician}     - Show technician detail
GET    /technicians/{technician}/edit - Show edit form
PUT    /technicians/{technician}     - Update technician
DELETE /technicians/{technician}     - Delete technician
```

**Controllers:** `TechnicianController`

---

### 3.5 Manajemen Layanan (Services)
**Use Case: Mengelola Layanan**
- ✅ List semua services (CRUD)
- ✅ Buat service baru
- ✅ Edit service
- ✅ Delete service
- ✅ Aktifkan/nonaktifkan service

**Routes:**
```
GET    /services              - List services
GET    /services/create       - Show create form
POST   /services              - Store new service
GET    /services/{service}    - Show service detail
GET    /services/{service}/edit - Show edit form
PUT    /services/{service}    - Update service
DELETE /services/{service}    - Delete service
```

**Controllers:** `ServiceController`

---

### 3.6 Manajemen Pemesanan (Bookings)
**Use Case: Mengelola Pemesanan**
- ✅ List semua bookings (paginated, filterable)
- ✅ Lihat detail booking
- ✅ Edit booking (assign technician, adjust price)
- ✅ Batalkan booking
- ✅ Tandai sebagai completed
- ✅ Filter berdasarkan status, customer, technician

**Routes:**
```
GET    /bookings                                - List bookings
GET    /bookings/{booking}                      - Show booking detail
GET    /bookings/{booking}/edit                 - Show edit form
PUT    /bookings/{booking}                      - Update booking
DELETE /bookings/{booking}                      - Delete booking
POST   /bookings/{booking}/cancel               - Cancel booking
POST   /bookings/{booking}/mark-completed       - Mark as completed
```

**Controllers:** `BookingController`

---

### 3.7 Manajemen Invoice
**Use Case: Mengelola Invoice/Tagihan**
- ✅ List semua invoices (paginated, filterable)
- ✅ Lihat detail invoice
- ✅ Edit invoice (adjust items, tax, discount)
- ✅ Tandai sebagai paid
- ✅ Generate/download invoice
- ✅ Filter berdasarkan status, customer

**Routes:**
```
GET    /invoices               - List invoices
GET    /invoices/{invoice}     - Show invoice detail
GET    /invoices/{invoice}/edit - Show edit form
PUT    /invoices/{invoice}     - Update invoice
DELETE /invoices/{invoice}     - Delete invoice
POST   /invoices/{invoice}/mark-paid - Mark invoice as paid
```

**Controllers:** `InvoiceController`

---

### 3.8 Manajemen Pembayaran
**Use Case: Mengelola Transaksi**
- ✅ List pending payments
- ✅ Lihat detail payment
- ✅ Approve payment
- ✅ Reject payment
- ✅ View payment history
- ✅ Filter berdasarkan status

**Routes:**
```
GET    /payments/pending              - List pending payments
GET    /payments/{payment}            - Show payment detail
POST   /payments/{payment}/approve    - Approve payment
POST   /payments/{payment}/reject     - Reject payment
GET    /api/payments/{payment}/status - Check payment status (API)
POST   /webhooks/midtrans             - Midtrans webhook (auto process)
```

**Payment Status:** `pending` → `approved` → `confirmed`

**Controllers:** `PaymentController`, `PaymentWebhookController`

---

### 3.9 Manajemen Slider (Homepage Carousel)
**Use Case: Setup Homepage**
- ✅ List all sliders
- ✅ Upload slider image
- ✅ Edit slider (title, description, link)
- ✅ Delete slider
- ✅ Toggle active/inactive
- ✅ Arrange order

**Routes:**
```
GET    /sliders                         - List sliders
GET    /sliders/create                  - Show create form
POST   /sliders                         - Store new slider
GET    /sliders/{slider}                - Show slider detail
GET    /sliders/{slider}/edit           - Show edit form
PUT    /sliders/{slider}                - Update slider
DELETE /sliders/{slider}                - Delete slider
POST   /sliders/{slider}/toggle-active  - Toggle active status
```

**Controllers:** `SliderController`

---

### 3.10 Reports & Analytics
**Use Case: Melihat Laporan & Analitik**
- ✅ Dashboard report dengan visualisasi
- ✅ Revenue report (by period, by service, by technician)
- ✅ Booking report (by status, by service, by technician)
- ✅ Technician performance report
- ✅ Customer behavior report
- ✅ Payment report (by status, by method)
- ✅ Export reports (PDF, Excel)

**Routes:**
```
GET /reports/dashboard    - Report dashboard
GET /reports/revenue      - Revenue analytics
GET /reports/bookings     - Booking statistics
GET /reports/technicians  - Technician performance
GET /reports/customers    - Customer statistics
GET /reports/payments     - Payment statistics
```

**Controllers:** `ReportController`

---

## 🔐 Middleware Role-Based Access Control

### Middleware Files
```
CheckAdminRole.php      - Validate user is admin
CheckCustomerRole.php   - Validate user is customer
CheckTechnicianRole.php - Validate user is technician
```

### Usage dalam Routes
```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes
});

Route::middleware(['auth', 'customer'])->group(function () {
    // Customer routes
});

Route::middleware(['auth', 'technician'])->group(function () {
    // Technician routes
});
```

---

## 🔗 User Journey Maps

### Customer Journey
```
Register/Login → Browse Services → Create Booking → 
Track Booking → Make Payment → Give Rating → Dashboard
```

### Technician Journey
```
Register/Login → Update Profile → View Assignments → 
Accept/Work on Bookings → Complete Work → View Ratings
```

### Admin Journey
```
Login → Dashboard → Manage Users/Services/Bookings → 
Handle Payments → View Reports → Dashboard
```

---

## 📊 Database Relationships

```
User (1) ─→ (Many) Booking (Customer)
User (1) ─→ (Many) Technician Profile
User (1) ─→ (Many) Rating

Booking (Many) ← (1) Service
Booking (Many) ← (1) Customer
Booking (Many) ← (1) Technician

Invoice (1) ← (1) Booking
Invoice (1) ← (Many) Payment

Rating (Many) → (1) Booking
Rating (Many) → (1) Technician
```

---

## ✅ Fitur Summary by Role

| Fitur | Customer | Technician | Admin |
|-------|----------|-----------|-------|
| **Autentikasi** | ✅ | ✅ | ✅ |
| **Manajemen Profil** | ✅ | ✅ | ✅ |
| **Browse Layanan** | ✅ | - | ✅ |
| **Create Booking** | ✅ | - | ✅ |
| **Manage Booking** | ✅ (own) | ✅ (assigned) | ✅ (all) |
| **Submit Payment** | ✅ | - | ✅ |
| **Approve Payment** | - | - | ✅ |
| **Give Rating** | ✅ | - | - |
| **View Rating** | ✅ | ✅ (own) | ✅ |
| **Dashboard** | ✅ | ✅ | ✅ |
| **Reports** | - | - | ✅ |
| **CRUD Users** | - | - | ✅ |
| **CRUD Services** | - | - | ✅ |
| **Manage Sliders** | - | - | ✅ |

---

**Last Updated:** April 26, 2026
**Version:** 1.0 - Complete Feature Mapping
