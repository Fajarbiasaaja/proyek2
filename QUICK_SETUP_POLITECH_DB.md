# ⚡ QUICK START: Setup Database Politech

## 3 Langkah:

### 1️⃣ Start MySQL (XAMPP)
- Buka **XAMPP Control Panel**
- Klik **"Start"** pada MySQL (sampai status hijau "Running")

### 2️⃣ Buat Database
- Double-click file: **`setup_politech_db.bat`** di folder proyek
- Atau jalankan manual:
```bash
php artisan migrate
```

### 3️⃣ Verifikasi
- Buka: http://localhost/phpmyadmin
- Cari database **"politech"**
- Selesai! ✅

---

**Status .env:** ✅ Sudah dikonfigurasi untuk database `politech`

Database akan berisi tabel:
- users, customers, technicians, services, bookings, invoices, sessions, cache, jobs, migrations

---

📖 **Full Guide:** Baca file [DATABASE_SETUP_POLITECH.md](DATABASE_SETUP_POLITECH.md)
