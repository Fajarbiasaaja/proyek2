# Setup Database MySQL "Politech" - Guide Lengkap

## Status Saat Ini
✅ File `.env` sudah dikonfigurasi untuk menggunakan database MySQL dengan nama **"politech"**

## 📋 Langkah-Langkah Setup (4 Langkah Mudah)

### Step 1: Buka XAMPP Control Panel
1. Buka **XAMPP Control Panel** di komputer Anda
2. Cari tombol **"Start"** pada baris **"MySQL"**
3. Klik tombol **"Start"** untuk menjalankan MySQL service
4. Status akan berubah menjadi warna **hijau** dan bertuliskan **"Running"**

![Contoh XAMPP CP](screenshot tidak tersedia - tapi tombolnya ada di kolom Actions sebelah kanan MySQL)

---

### Step 2: Verifikasi MySQL Berjalan
Buka PowerShell/Terminal dan jalankan:
```bash
cd C:\xampp\htdocs\proyek2
php artisan tinker
```

Jika keluar prompt `>>>` tanpa error, berarti MySQL sudah connected ✅

---

### Step 3: Buat Database "Politech"

**Opsi A: Auto Setup (Recommended) ⭐**
1. Buka File Explorer
2. Navigate ke: `C:\xampp\htdocs\proyek2`
3. Double-click file: `setup_politech_db.bat`
4. Tunggu script selesai
5. Database "politech" akan dibuat otomatis + migration berjalan

**Opsi B: Manual Setup**
Jalankan commands di PowerShell:
```bash
cd C:\xampp\htdocs\proyek2

# Method 1: Artisan command (requires doctrine/dbal)
php artisan db:create

# Method 2: Direct MySQL command
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS politech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

### Step 4: Jalankan Migration
Setelah database politech berhasil dibuat, jalankan:

```bash
cd C:\xampp\htdocs\proyek2
php artisan migrate
```

Output yang benar akan terlihat seperti:
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table (XXXms)
...semua migration files...
Migrated:  2025_02_18_100000_add_oauth_fields_to_users_table (XXXms)
```

✅ **Selesai!** Database "politech" sudah siap dengan semua schema.

---

## 🔍 Verifikasi Database Berhasil Dibuat

### Via MySQL Command
```bash
"C:\xampp\mysql\bin\mysql.exe" -u root -e "SHOW DATABASES LIKE 'politech'; USE politech; SHOW TABLES;"
```

### Via PhpMyAdmin
1. Buka browser: `http://localhost/phpmyadmin`
2. Login (username: root, password: kosong)
3. Cari database "politech" di sidebar kiri
4. Klik untuk melihat semua tables

---

## 📊 Database Structure

Tabel yang akan dibuat:
- `users` - Data pengguna (admin, customer, technician)
- `customers` - Data pelanggan
- `technicians` - Data teknisi
- `services` - Data layanan
- `bookings` - Data booking servis
- `invoices` - Data invoice
- `cache` - Cache system
- `jobs` - Queue jobs
- `migrations` - Migration history
- `sessions` - Session data

---

## 🚨 Troubleshooting

### ❌ Error: "Can't connect to MySQL server"
**Solusi:**
1. Buka XAMPP Control Panel
2. Klik "Start" untuk MySQL
3. Tunggu 3-5 detik sampai status "Running"
4. Coba jalankan migration lagi

### ❌ Error: "Database politech doesn't exist"
**Solusi:**
1. Pastikan MySQL sudah running (status hijau di XAMPP)
2. Re-run setup script: `setup_politech_db.bat`
3. Atau manual create: 
```bash
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE politech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### ❌ Error: Migration stuck/error
**Solusi:**
1. Restore from backup atau restart
2. Atau fresh install:
```bash
php artisan migrate:fresh
```
⚠️ Ini akan delete semua data di politech database!

### ❌ PhpMyAdmin tidak bisa access
**Solusi:**
1. Pastikan Apache juga running di XAMPP
2. Buka: `http://localhost/phpmyadmin`
3. Kalau tidak bisa, restart Apache dari XAMPP CP

---

## 💾 Backup & Restore

### Backup Database
```bash
"C:\xampp\mysql\bin\mysqldump.exe" -u root politech > C:\xampp\htdocs\proyek2\backup_politech.sql
```

### Restore Database
```bash
"C:\xampp\mysql\bin\mysql.exe" -u root politech < C:\xampp\htdocs\proyek2\backup_politech.sql
```

---

## 📝 Catatan Penting

- ✅ Password MySQL default di XAMPP adalah **kosong** (tidak ada password)
- ✅ Database charset: **utf8mb4** (support emoji, special characters)
- ✅ Database collation: **utf8mb4_unicode_ci**
- ✅ Semua migrations otomatis tertanam di `database/migrations/`
- ✅ Database "politech" bisa diakses dari phpmyadmin

---

## 🎯 Next Steps

Setelah database setup selesai:
1. Jalankan server: `php artisan serve`
2. Buka browser: `http://localhost:8000`
3. Login atau register dengan akun baru
4. Data akan tersimpan di database"politech"

---

**Database "politech" siap digunakan!** 🎉
