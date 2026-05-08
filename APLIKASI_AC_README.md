# Aplikasi Jasa Servis AC

Aplikasi manajemen jasa servis AC yang dibangun dengan Laravel 11 dan Bootstrap 5. Aplikasi ini dirancang untuk memudahkan pengelolaan pelanggan, teknisi, layanan servis, pemesanan, dan invoicing.

## 🎯 Fitur Utama

### 1. **Dashboard**
- Tampilan ringkasan statistik penting
- Total pelanggan, pemesanan, dan pendapatan
- Daftar pemesanan terbaru
- Statistik teknisi dan layanan

### 2. **Manajemen Pelanggan**
- CRUD (Create, Read, Update, Delete) pelanggan
- Menyimpan informasi kontak dan alamat
- Riwayat pemesanan pelanggan
- Email dan nomor telepon unik

### 3. **Manajemen Teknisi**
- CRUD teknisi dengan spesialisasi
- Status ketersediaan (Available, Busy, Inactive)
- Riwayat pekerjaan / pemesanan
- Tracking pekerjaan per teknisi

### 4. **Manajemen Layanan**
- CRUD jenis-jenis layanan AC
- Harga dan durasi layanan yang fleksibel
- Daftar layanan yang tersedia
- Detail pemesanan per layanan

### 5. **Manajemen Pemesanan**
- Buat pemesanan baru dengan pelanggan, layanan, dan teknisi
- Update status pemesanan (Pending, Confirmed, In Progress, Completed, Cancelled)
- Assign teknisi ke pemesanan
- Catatan pemesanan dan penyelesaian
- Automatic invoice generation saat pemesanan selesai

### 6. **Manajemen Invoice**
- Auto-generated invoice untuk setiap pemesanan selesai
- Tracking status pembayaran (Draft, Issued, Paid, Overdue)
- Perhitungan pajak otomatis (10%)
- Riwayat pembayaran per invoice

## 📋 Data dan Tabel Database

### Tabel Utama:
1. **customers** - Data pelanggan
2. **technicians** - Data teknisi
3. **services** - Jenis-jenis layanan
4. **bookings** - Pemesanan servis
5. **invoices** - Invoice/tagihan

## 🚀 Cara Menggunakan

### Instalasi & Setup

```bash
# 1. Navigate ke folder project
cd c:\xampp\htdocs\proyek2

# 2. Install dependencies (jika belum)
composer install
npm install

# 3. Copy file .env (jika belum ada)
cp .env.example .env

# 4. Generate APP_KEY
php artisan key:generate

# 5. Jalankan migrasi dan seeding
php artisan migrate --seed

# 6. Build assets
npm run build

# 7. Jalankan development server
php artisan serve
```

### Akses Aplikasi

- **Dashboard**: Buka browser ke `http://localhost:8000/dashboard`
- **URL Awal**: `http://localhost:8000/`

## 📁 Struktur File

```
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php
│       ├── CustomerController.php
│       ├── TechnicianController.php
│       ├── ServiceController.php
│       ├── BookingController.php
│       └── InvoiceController.php
├── Models/
│   ├── Customer.php
│   ├── Technician.php
│   ├── Service.php
│   ├── Booking.php
│   └── Invoice.php

database/
├── migrations/
│   ├── 2025_02_17_000010_create_technicians_table.php
│   ├── 2025_02_17_000011_create_customers_table.php
│   ├── 2025_02_17_000012_create_services_table.php
│   ├── 2025_02_17_000013_create_bookings_table.php
│   └── 2025_02_17_000014_create_invoices_table.php
├── seeders/
│   ├── CustomerSeeder.php
│   ├── TechnicianSeeder.php
│   ├── ServiceSeeder.php
│   └── DatabaseSeeder.php

resources/
└── views/
    ├── layouts/
    │   └── app.blade.php (Layout utama)
    ├── dashboard.blade.php
    ├── customers/
    ├── technicians/
    ├── services/
    ├── bookings/
    └── invoices/

routes/
└── web.php (Routing)
```

## 🔗 Route / Menu Utama

| Menu | Route | Fungsi |
|------|-------|--------|
| Dashboard | `/dashboard` | Ringkasan statistik |
| Pelanggan | `/customers` | CRUD pelanggan |
| Teknisi | `/technicians` | CRUD teknisi |
| Layanan | `/services` | CRUD layanan |
| Pemesanan | `/bookings` | CRUD pemesanan |
| Invoice | `/invoices` | Manajemen invoice |

## 📊 Workflow Pemesanan

1. **Buat Pemesanan** - Pelanggan membuat pemesanan dengan memilih layanan dan teknisi
2. **Konfirmasi** - Admin mengkonfirmasi pemesanan dan assign teknisi
3. **Proses** - Teknisi mengerjakan servis (status: In Progress)
4. **Selesai** - Pemesanan ditandai selesai → **Invoice otomatis terbuat**
5. **Pembayaran** - Invoice ditandai dibayar

## 🎨 User Interface

- **Responsive Design** - Bekerja di desktop dan mobile
- **Bootstrap 5** - Framework CSS modern
- **Bootstrap Icons** - Ikon profesional
- **Sidebar Navigation** - Menu navigasi yang user-friendly
- **Color Coded Status** - Badge berwarna untuk status

## 👤 Status Teknisi

- 🟢 **Available** - Siap menerima pekerjaan
- 🟡 **Busy** - Sedang mengerjakan pekerjaan
- 🔴 **Inactive** - Tidak aktif

## 💰 Fitur Finance

- Harga otomatis dari layanan yang dipilih
- Perhitungan pajak 10% otomatis
- Multiple status invoice (Draft, Issued, Paid, Overdue)
- Tracking pembayaran dengan tanggal pembayaran

## 📝 Data Test

Aplikasi sudah dilengkapi dengan data test:
- 5 pelanggan
- 5 teknisi dengan spesialisasi berbeda
- 7 jenis layanan AC

## 🛠️ Teknologi

- **Backend**: Laravel 11
- **Database**: SQLite (default) / MySQL
- **Frontend**: Blade Template, Bootstrap 5
- **Icons**: Bootstrap Icons
- **PHP Version**: 8.0+

## 📞 Kontak & Support

Untuk pertanyaan atau bug report, silakan hubungi developer.

---

**Aplikasi Jasa Servis AC v1.0**  
Dibuat: Februari 2025
