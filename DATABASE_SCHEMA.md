# Database Schema Documentation

## Overview
Dokumentasi lengkap struktur database untuk aplikasi JASAKU (Layanan AC Profesional).

## Tables & Relationships

### 1. users
Tabel utama untuk authentication semua role (admin, customer, technician).

**Columns:**
- `id` (Primary Key)
- `name` (varchar) - Nama user
- `email` (varchar, unique) - Email untuk login
- `password` (varchar, hashed)
- `role` (enum) - admin, customer, technician
- `provider` (varchar, nullable) - OAuth provider (google, facebook, github)
- `provider_id` (varchar, nullable) - ID dari OAuth provider
- `oauth_data` (json, nullable) - Data tambahan dari OAuth provider
- `email_verified_at` (timestamp, nullable)
- `remember_token` (varchar, nullable)
- `created_at, updated_at` (timestamp)

**Relationships:**
- `hasOne(Customer)` - User dengan role 'customer'
- `hasOne(Technician)` - User dengan role 'technician'

---

### 2. customers
Tabel untuk data customer/pelanggan.

**Columns:**
- `id` (Primary Key)
- `user_id` (Foreign Key → users.id) - Reference ke user
- `phone` (varchar) - Nomor telepon
- `address` (text) - Alamat rumah/lokasi
- `city` (varchar) - Kota
- `postal_code` (varchar) - Kode pos
- `latitude` (decimal) - Koordinat lokasi (untuk mapping)
- `longitude` (decimal) - Koordinat lokasi
- `total_bookings` (integer, default 0) - Total booking count
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable) - Soft delete

**Relationships:**
- `belongsTo(User)` - User yang jadi customer
- `hasMany(Booking)` - Semua booking dari customer ini

---

### 3. technicians
Tabel untuk data teknisi/penyedia jasa.

**Columns:**
- `id` (Primary Key)
- `user_id` (Foreign Key → users.id, nullable) - Optional link ke user
- `name` (varchar)
- `phone` (varchar)
- `email` (varchar) - Email untuk komunikasi  
- `address` (text) - Alamat rumah teknisi
- `specialization` (varchar, nullable) - Keahlian (contoh: "AC Central", "Window Unit")
- `status` (enum) - available, busy, off
- `is_active` (boolean, default true) - Status aktif/tidak
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable) - Soft delete

**Relationships:**
- `belongsTo(User, 'user_id')` - Link ke user jika ada
- `hasMany(Booking)` - Semua booking yang ditangani
- `hasMany(Rating)` - Semua rating yang diterima

---

### 4. services
Katalog layanan/jenis servis yang ditawarkan.

**Columns:**
- `id` (Primary Key)
- `name` (varchar) - Nama layanan (contoh: "Cleaning Service", "Gas Refill")
- `description` (text, nullable) - Deskripsi detail layanan
- `category` (varchar) - Kategori (maintenance, repair, installation)
- `base_price` (decimal) - Harga dasar
- `duration_minutes` (integer) - Estimasi durasi (dalam menit)
- `is_active` (boolean, default true) - Tersedia atau tidak
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable)

**Relationships:**
- `hasMany(Booking)` - Semua booking untuk service ini

---

### 5. bookings
Order/pemesanan servis dari customer.

**Columns:**
- `id` (Primary Key)
- `customer_id` (Foreign Key → customers.id)
- `service_id` (Foreign Key → services.id)
- `technician_id` (Foreign Key → technicians.id, nullable) - Teknisi yang ditugaskan
- `scheduled_date` (datetime) - Jadwal servis
- `notes` (text) - Catatan khusus dari customer
- `status` (enum) - pending, confirmed, in_progress, completed, cancelled
- `total_price` (decimal) - Harga total servis
- `completion_notes` (text, nullable) - Catatan hasil servis dari teknisi
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable) - Soft delete

**Workflow Status:**
```
pending → confirmed → in_progress → completed
                   ↓
                cancelled (dari mana saja)
```

**Relationships:**
- `belongsTo(Customer)` - Customer yang melakukan booking
- `belongsTo(Service)` - Service yang dipesan
- `belongsTo(Technician)` - Teknisi yang ditugaskan
- `hasOne(Invoice)` - Invoice untuk pembayaran
- `hasOne(Rating)` - Rating dari customer setelah selesai

---

### 6. invoices
Tagihan/invoice untuk setiap booking.

**Columns:**
- `id` (Primary Key)
- `booking_id` (Foreign Key → bookings.id)
- `invoice_number` (varchar, unique) - Nomor invoice
- `amount` (decimal) - Total amount
- `status` (enum) - pending, paid, cancelled
- `due_date` (datetime)
- `paid_at` (datetime, nullable) - Waktu pembayaran
- `notes` (text, nullable)
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable)

**Relationships:**
- `belongsTo(Booking)` - Booking yang di-invoice
- `hasMany(Payment)` - Pembayaran untuk invoice ini

---

### 7. payments
Transaksi pembayaran/bukti pembayaran invoice.

**Columns:**
- `id` (Primary Key)
- `invoice_id` (Foreign Key → invoices.id)
- `amount` (decimal)
- `payment_method` (varchar) - bank_transfer, e_wallet, cash, etc
- `reference_number` (varchar) - Reference/receipt number
- `status` (enum) - pending, approved, rejected
- `payment_proof` (varchar, nullable) - Path ke bukti pembayaran (foto/dokumen)
- `notes` (text, nullable)
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable) 

**Relationships:**
- `belongsTo(Invoice)` - Invoice yang dibayar

---

### 8. ratings
Rating dan review dari customer terhadap service/technician.

**Columns:**
- `id` (Primary Key)
- `booking_id` (Foreign Key → bookings.id)
- `customer_id` (Foreign Key → customers.id)
- `technician_id` (Foreign Key → technicians.id)
- `rating` (tinyint) - 1-5 stars
- `review` (text, nullable) - Review teks
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable)

**Relationships:**
- `belongsTo(Booking)` - Booking yang di-rate
- `belongsTo(Customer)` - Customer pemberi rating
- `belongsTo(Technician)` - Technician penerima rating

---

### 9. sliders
Homepage carousel images untuk featured services.

**Columns:**
- `id` (Primary Key)
- `title` (varchar)
- `description` (text, nullable)
- `image_path` (varchar) - Path ke image file
- `link` (varchar, nullable) - Link destination
- `position` (integer) - Urutan tampilan
- `is_active` (boolean) - Aktif/tidak
- `created_at, updated_at` (timestamp)
- `deleted_at` (timestamp, nullable)

---

## ER Diagram Simplified

```
┌─────────────┐
│    users    │
├─────────────┤
│ id          │
│ name        │
│ email       │
│ role        │
│ provider    │
└──────┬──────┘
       │
       ├──→ customers
       │    ├──→ bookings ──→ services
       │    │      ├──→ invoices ──→ payments
       │    │      └──→ ratings ←─┘
       │    └──→ ratings
       │
       └──→ technicians
            ├──→ bookings
            └──→ ratings

sliders (independent)
```

## Key Design Decisions

1. **Soft Deletes**: Customers, Technicians, Bookings, Invoices, Payments, Ratings menggunakan soft delete untuk audit trail.

2. **Role-based**: User table memiliki `role` column untuk membedakan admin, customer, dan technician.

3. **OAuth Support**: Users dapat login via OAuth (Google, Facebook, GitHub) dengan fields tambahan di users table.

4. **Booking Workflow**: Status booking memiliki workflow yang jelas untuk tracking progress.

5. **Financial Tracking**: Booking → Invoice → Payment untuk clear financial tracking.

6. **Rating System**: Rating tabel untuk customer reviews dan technician ratings.

7. **Flexible Technician Link**: Technician bisa link ke User (jika login online) atau standalone (jika tidak perlu login).

---

## Migration Files

- `0001_01_01_000000_create_users_table` - Users
- `0001_01_01_000001_create_cache_table` - Cache (Laravel built-in)
- `0001_01_01_000002_create_jobs_table` - Jobs queue (Laravel built-in)
- `2025_02_17_000010_create_technicians_table` - Technicians
- `2025_02_17_000011_create_customers_table` - Customers
- `2025_02_17_000012_create_services_table` - Services
- `2025_02_17_000013_create_bookings_table` - Bookings
- `2025_02_17_000014_create_invoices_table` - Invoices
- `2025_02_17_000015_add_role_to_users_table` - Add role column
- `2025_02_18_100000_add_oauth_fields_to_users_table` - OAuth support
- `2025_02_18_110000_create_sliders_table` - Sliders
- `2025_02_18_120000_create_payments_table` - Payments
- `2025_02_21_000015_create_ratings_table` - Ratings

---

## Indexing Strategy

Key indexes untuk performance:
- `users(email, role)` - Fast authentication
- `bookings(customer_id, status)` - Customer booking queries
- `bookings(technician_id, status)` - Technician task queries
- `ratings(technician_id, created_at)` - Get technician ratings
- `invoices(booking_id, status)` - Invoice tracking
- `payments(invoice_id, status)` - Payment tracking

