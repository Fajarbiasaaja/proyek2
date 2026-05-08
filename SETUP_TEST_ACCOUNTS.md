# 🚀 Setup Test Accounts - Langkah demi Langkah

## Masalah
Akun-akun yang tercantum di TEST_ACCOUNTS_LIST.md belum terdaftar di database. 
Anda perlu menjalankan **database seeder** untuk membuat mereka.

---

## ✅ SOLUSI: Jalankan Seeder

### Option 1: Reset Database + Seed (Recommended)
Jika Anda baru pertama kali setup dan tidak ada data penting:

```bash
# Terminal / Command Prompt / PowerShell
cd c:\xampp\htdocs\proyek2

# Reset database dan jalankan semua seeder
php artisan migrate:refresh --seed
```

**Apa yang terjadi:**
1. ✅ Hapus semua tabel (dangerous jika ada data penting!)
2. ✅ Buat ulang tabel dari migrations
3. ✅ Jalankan semua seeders termasuk UserSeeder
4. ✅ 11 akun test otomatis terbuat

**Time:** ~30 detik

---

### Option 2: Seed Saja (Jika sudah ada data)
Jika sudah ada booking/data penting yang ingin dipertahankan:

```bash
# Hanya jalankan seeder tanpa reset
php artisan db:seed --class=UserSeeder
```

**Apa yang terjadi:**
1. ✅ Tambahkan akun-akun ke database
2. ✅ Tidak hapus data yang sudah ada

**Time:** ~5 detik

---

### Option 3: Manual via SQL (Backup Plan)
Jika Anda lebih nyaman dengan SQL:

```sql
-- Buka phpMyAdmin atau MySQL client
-- Pilih database proyek2

-- ADMIN ACCOUNTS
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('Admin Super', 'admin@jasaku.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'admin', NOW(), NOW(), NOW()),
('Admin Secondary', 'admin2@jasaku.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'admin', NOW(), NOW(), NOW());

-- CUSTOMER ACCOUNTS (4)
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('Customer One', 'customer1@example.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'customer', NOW(), NOW(), NOW()),
('Budi Customer', 'budi@example.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'customer', NOW(), NOW(), NOW()),
('Siti Customer', 'siti@example.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'customer', NOW(), NOW(), NOW()),
('Test Customer', 'test.customer@example.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'customer', NOW(), NOW(), NOW());

-- TECHNICIAN ACCOUNTS (5)
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('Ahmad Teknisi', 'ahmad.teknisi@provider.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'technician', NOW(), NOW(), NOW()),
('Budi Service', 'budi.service@provider.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'technician', NOW(), NOW(), NOW()),
('Doni Maintenance', 'doni.maintenance@provider.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'technician', NOW(), NOW(), NOW()),
('Roni Junior', 'roni.junior@provider.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'technician', NOW(), NOW(), NOW()),
('Hendra Professional', 'hendra.pro@provider.com', '$2y$12$N9qo8uLOickgx2ZMRZoMye', 'technician', NOW(), NOW(), NOW());
```

**Catatan:** Password hash di atas (`$2y$12$...`) adalah contoh. Gunakan Artisan tinker untuk generate hash yang benar.

---

## 🔧 Langkah-Langkah Detail

### Step 1: Buka Terminal/Command Prompt

**Windows:**
```
Win + R
type: cmd
press Enter
```

**atau buka dari VS Code:**
```
Ctrl + ` (backtick)
```

---

### Step 2: Navigasi ke Project Folder

```bash
cd c:\xampp\htdocs\proyek2
```

Verifikasi:
```bash
dir
```
Harus muncul: `artisan`, `config`, `app`, `database`, dll.

---

### Step 3: Jalankan Seeder

#### Pilihan A: Reset Database (PALING MUDAH)
```bash
php artisan migrate:refresh --seed
```

Output yang diharapkan:
```
Rolled back: [semua migrations]
Migrated: [semua migrations]
Seeding: Database\Seeders\DatabaseSeeder
Seeded: Database\Seeders\UserSeeder
Seeded: Database\Seeders\CustomerSeeder
Seeded: Database\Seeders\TechnicianSeeder
[...more seeders...]
Database seeding completed successfully.
```

#### Pilihan B: Seed Only
```bash
php artisan db:seed --class=UserSeeder
```

Output:
```
Seeding: Database\Seeders\UserSeeder
Seeded: Database\Seeders\UserSeeder
Database seeding completed successfully.
```

---

### Step 4: Verifikasi Akun Terbuat

**Via Artisan Tinker:**
```bash
php artisan tinker
>>> User::count()
=> 11

>>> User::pluck('email')
=> Illuminate\Support\Collection {#...
  "admin@jasaku.com",
  "admin2@jasaku.com",
  "customer1@example.com",
  ...
}

>>> exit
```

**Via phpMyAdmin:**
1. Buka: http://localhost/phpmyadmin
2. Pilih database: `proyek2`
3. Buka table: `users`
4. Harus ada 11 baris dengan email dari daftar di atas

---

## ✅ Akun yang Akan Terbuat

### ADMIN (2)
```
1. admin@jasaku.com           → Password: Admin@123456
2. admin2@jasaku.com          → Password: SecurePass123!
```

### CUSTOMER (4)
```
3. customer1@example.com      → Password: Customer@123
4. budi@example.com           → Password: Budi@12345
5. siti@example.com           → Password: Siti@12345
6. test.customer@example.com  → Password: TestPass@123
```

### TECHNICIAN (5)
```
7. ahmad.teknisi@provider.com      → Password: TechPass@123
8. budi.service@provider.com       → Password: Service@123
9. doni.maintenance@provider.com   → Password: Maintain@123
10. roni.junior@provider.com        → Password: Junior@123
11. hendra.pro@provider.com         → Password: ProTech@123
```

---

## 🧪 Test Login Setelah Seeder

### 1. Buka Browser
```
http://localhost:8000
```

### 2. Test Admin Login
```
Email:    admin@jasaku.com
Password: Admin@123456
Klik:     Login
```

Expected: Redirect ke `/dashboard` (admin dashboard)

### 3. Test Technician Login
```
Go to: http://localhost:8000/login/technician
Email:    hendra.pro@provider.com
Password: ProTech@123
Klik:     Login
```

Expected: Redirect ke `/technician/dashboard`

### 4. Test Customer Login
```
Go to: http://localhost:8000/login
Email:    customer1@example.com
Password: Customer@123
Klik:     Login
```

Expected: Redirect ke `/customer/dashboard`

---

## 🆘 Troubleshooting

### Error: "No such table: users"
```
Solution: Jalankan migrations terlebih dahulu
php artisan migrate
```

### Error: "SQLSTATE[HY000]: General error: 1030 Got error..."
```
Solution: Truncate tables dulu
php artisan migrate:reset
php artisan migrate
php artisan db:seed
```

### Error: "Illuminate\Database\QueryException"
```
Solution: Pastikan database connection di .env sudah benar
cat .env | grep DATABASE
```

### Password salah saat login
```
Solution: Pastikan password yang digunakan sesuai yang di TEST_ACCOUNTS_LIST.md
- Jangan pakai "password" (itu sudah deprecated)
- Gunakan password di USER SEEDER baru
```

---

## 📝 Checklist

- [ ] Buka terminal di `c:\xampp\htdocs\proyek2`
- [ ] Jalankan: `php artisan migrate:refresh --seed`
- [ ] Tunggu sampai "Database seeding completed successfully"
- [ ] Buka: http://localhost:8000
- [ ] Test login dengan admin@jasaku.com / Admin@123456
- [ ] Test login dengan hendra.pro@provider.com / ProTech@123 (technician)
- [ ] Test login dengan customer1@example.com / Customer@123 (customer)
- [ ] ✅ Semua akun berhasil terbuat!

---

## 🎯 Next Steps

Setelah akun terbuat:

1. **Test Admin Dashboard**
   - Login: admin@jasaku.com
   - Verify dapat melihat: Customers, Technicians, Services, Bookings

2. **Test Customer Booking**
   - Login: customer1@example.com
   - Create booking
   - Submit payment

3. **Test Technician Work**
   - Login: ahmad.teknisi@provider.com
   - View assigned bookings
   - Complete booking

4. **Test Admin Approval**
   - Back to admin@jasaku.com
   - Approve payment
   - View reports

---

**Updated:** April 26, 2026
**Status:** ✅ Ready to Seed
