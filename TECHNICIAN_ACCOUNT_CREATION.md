# 📋 Panduan Membuat Akun Teknisi

## Ringkasan
Teknisi di sistem ini menggunakan **dua table terintegrasi**:
1. **`users`** table - untuk authentication/login
2. **`technicians`** table - untuk data profile & assignment

Ketika admin membuat akun teknisi baru, **kedua table otomatis ter-update**.

---

## 🔑 Cara Membuat Akun Teknisi

### **Metode 1: Via Admin Dashboard (Recommended)**

#### Langkah-langkah:

1. **Login sebagai Admin**
   - Buka `/login`
   - Gunakan akun admin
   - Dashboard akan muncul

2. **Akses Menu Teknisi**
   - Klik **Menu Utama** → **Kelola Teknisi**
   - Atau buka URL: `/technicians`
   - Lihat list semua teknisi yang ada

3. **Klik Tombol "Tambah Teknisi Baru"**
   - Tombol berwarna hijau dengan icon `+`
   - URL: `/technicians/create`

4. **Isi Form Pendaftaran Teknisi**

   **Field yang harus diisi (`*` = wajib):**
   
   | Field | Keterangan | Contoh |
   |-------|-----------|--------|
   | **Nama*** | Nama lengkap teknisi | Budi Santoso |
   | **Telepon*** | Nomor HP/kontak | 08123456789 |
   | **Email*** | Email unik untuk login | budi@tech.com |
   | **Alamat** | Alamat rumah/base (optional) | Jl. Merdeka No. 10 |
   | **Spesialisasi*** | Keahlian/tipe servis | AC Split, Maintenance |
   | **Password*** | Password untuk login | MinimalEmail6Karakter |
   | **Status*** | Kondisi ketersediaan | Tersedia / Sibuk / Tidak Aktif |

5. **Contoh Pengisian Form:**
   ```
   Nama:              Budi Santoso
   Telepon:           0812-3456-789
   Email:             budi.santoso@technic.id
   Alamat:            Jl. Ahmad Yani No. 42, Jakarta
   Spesialisasi:      AC Split, AC Central
   Password:          BudiPassword123!
   Status:            Tersedia
   ```

6. **Klik Tombol "Simpan"**
   - Form divalidasi
   - Jika ada error, form akan menunjukkan pesan error (field berwarna merah)
   - Jika sukses → redirect ke list teknisi dengan notifikasi hijau

7. **Lihat Notifikasi Sukses**
   ```
   ✓ Teknisi berhasil ditambahkan! User account telah dibuat.
   Teknisi dapat login di /login/technician dengan email: budi.santoso@technic.id
   ```

---

## 🔓 Teknisi Login

### **Login URL:**
- **Page:** `/login/technician`
- **Method:** Email + Password

### **Langkah-langkah Login Teknisi:**

1. **Buka halaman login teknisi**
   - URL: `http://localhost:8000/login/technician`
   - Atau klik link "Login Teknisi" di halaman utama

2. **Masukkan Credentials**
   ```
   Email:    budi.santoso@technic.id
   Password: BudiPassword123!
   ```

3. **Klik Tombol "Masuk"**
   - Sistem validasi email & password
   - Jika salah → error message: "Email atau password salah"
   - Jika benar → redirect ke **Technician Dashboard**

4. **Akses Technician Dashboard**
   - URL: `/technician/dashboard`
   - Menampilkan:
     - 📊 **Stats**: Total Booking, Active, Completed, Rating
     - 📅 **Tab Navigation**:
       - Booking Aktif - pekerjaan saat ini
       - Jadwal Mendatang - upcoming jobs
       - Selesai - work history
   - 🎯 **Quick Actions**: View All, Edit Profile, Logout

---

## 🔐 Password & Security

### **Persyaratan Password:**
- ✓ Minimal **6 karakter**
- ✓ Boleh kombinasi: huruf, angka, simbol
- ✓ Case-sensitive (membedakan A dan a)
- ✗ Jangan password yang mudah ditebak

### **Contoh Password yang Baik:**
```
✓ BudiTech2024!
✓ Servis123Ac
✓ TechAc@2024
✓ RajaServisAC99
```

### **Contoh Password yang BURUK:**
```
✗ 123456 (terlalu simple)
✗ password (terlalu umum)
✗ 12345 (kurang dari 6 karakter)
✗ abc (terlalu pendek)
```

---

## 📋 Data yang Tersimpan

Ketika membuat akun teknisi, sistem menyimpan data di **dua tempat**:

### **1. `users` Table (Database Auth)**
```sql
INSERT INTO users (name, email, password, role, created_at, updated_at)
VALUES (
  'Budi Santoso',
  'budi.santoso@technic.id',
  'hash_password_aman',
  'technician',
  NOW(),
  NOW()
);
```

Data yang disimpan:
- `id` - User ID unik
- `name` - Nama lengkap
- `email` - Email unik (untuk login)
- `password` - Password ter-hash (tidak tersimpan plain text!)
- `role` - Role = 'technician'
- `created_at`, `updated_at` - Timestamps

### **2. `technicians` Table (Profile Data)**
```sql
INSERT INTO technicians (name, phone, email, address, specialization, status, created_at, updated_at)
VALUES (
  'Budi Santoso',
  '08123456789',
  'budi.santoso@technic.id',
  'Jl. Ahmad Yani No. 42, Jakarta',
  'AC Split, AC Central',
  'available',
  NOW(),
  NOW()
);
```

Data yang disimpan:
- `id` - Technician ID unik
- `name` - Nama lengkap
- `phone` - Nomor kontak
- `email` - Email (sama seperti di users table)
- `address` - Alamat rumah/base
- `specialization` - Keahlian/tipe servis
- `status` - Availability status (available/busy/inactive)
- `created_at`, `updated_at` - Timestamps

---

## ⚠️ Validasi & Error Handling

### **Error: Email sudah digunakan**
```
Error: Email atau password salah.
```
**Penyebab:** Email sudah terdaftar di:
- Tabel `users` (sebagai user account)
- Tabel `technicians` (sebagai technician profile)

**Solusi:**
- Gunakan email yang berbeda
- Atau update email di database jika duplikat

### **Error: Password terlalu pendek**
```
Error: The password must be at least 6 characters.
```
**Solusi:** Gunakan password minimal 6 karakter

### **Error: Email format tidak valid**
```
Error: The email must be a valid email address.
```
**Solusi:** 
- Email harus format: `nama@domain.com`
- Tidak boleh spasi atau karakter khusus di depan/belakang

### **Error: Field required**
```
Error: The [field name] field is required.
```
**Solusi:** Isi semua field yang bertanda `*`

---

## 🔄 Update/Edit Akun Teknisi

### **Cara Edit Data Teknisi (Profile):**

1. **Menu Teknisi** → Lihat list → Klik icon **Edit** (pensil)
2. Form edit hanya update field profile:
   - Nama
   - Telepon
   - Email
   - Alamat
   - Spesialisasi
   - Status

3. **Note:** Password TIDAK bisa diedit dari form ini
   - Teknisi harus gunakan feature "Change Password" di profile mereka sendiri
   - Atau admin reset via database

### **Cara Reset/Ubah Password Teknisi:**

**Opsi 1: Teknisi ubah sendiri**
- Login ke `/technician/dashboard`
- Klik **Edit Profile** atau **Settings**
- Form "Change Password"
- Masukkan password baru & confirm

**Opsi 2: Admin reset via phpmyadmin/database**
```sql
UPDATE users 
SET password = SHA2('NewPassword123', 256)
WHERE email = 'budi.santoso@technic.id' AND role = 'technician';
```

**Opsi 3: Manual backup & restore password**
```bash
# Di terminal/command line (jika Laravel Tinker tersedia)
php artisan tinker

$user = User::where('email', 'budi.santoso@technic.id')->first();
$user->password = Hash::make('NewPassword123!');
$user->save();
```

---

## 📊 Status Teknisi

Teknisi memiliki **3 status ketersediaan**:

| Status | Keterangan | Booking Baru |
|--------|-----------|------------|
| **available** 🟢 | Siap menerima pekerjaan | ✓ Bisa di-assign |
| **busy** 🟡 | Sedang ada pekerjaan | ⚠️ Manual assign only |
| **inactive** 🔴 | Tidak aktif/cuti | ✗ Tidak bisa di-assign |

**Cara ubah status:**
1. Menu Teknisi → klik nama teknisi
2. Klik tombol **Edit**
3. Rubah field **Status**
4. Klik **Simpan**

---

## 🗑️ Hapus Akun Teknisi

### **Soft Delete (Recommended):**
1. Menu Teknisi → Klik nama technician
2. Klik tombol **Hapus** merah atau icon trash
3. Konfirmasi "Yakin hapus?"
4. Teknisi ter-'soft delete' (tidak muncul di list, tapi data tetap ada di DB)

### **Permanent Delete (Jarang):**
```sql
-- Soft delete (bisa restore)
DELETE FROM technicians WHERE id = 1;

-- Hard delete (tidak bisa restore) - HATI-HATI!
DELETE FROM users WHERE id = 1 AND role = 'technician';
DELETE FROM technicians WHERE id = 1;
```

**⚠️ PENTING:** Hard delete akan menghilangkan history booking teknisi!

---

## 📱 Lupa Password?

Jika teknisi lupa password, saat ini **belum ada fitur "Forgot Password"**.

**Solusi sementara:**
1. Admin reset password via database (lihat section "Edit Akun Teknisi")
2. Admin beri password temporary ke teknisi
3. Teknisi login & ubah password sendiri via "Change Password"

---

## ✅ Checklist Membuat Akun Teknisi

- [ ] Admin login dan akses `/technicians/create`
- [ ] Isi form dengan data lengkap & benar
- [ ] Pastikan email belum terdaftar (unique)
- [ ] Gunakan password yang kuat (min 6 char)
- [ ] Klik **Simpan**
- [ ] Lihat notifikasi sukses hijau
- [ ] Komunikasikan credentials ke teknisi
- [ ] Teknisi login di `/login/technician`
- [ ] Teknisi dapat akses dashboard & view bookings

---

## 🎯 Testing & Verification

### **Test Scenario:**
```
1. Login Admin: admin@example.com / password
2. Tambah teknisi: 
   - Nama: Test Technician
   - Email: test.tech@example.com
   - Password: TestPass123
3. Logout
4. Login Technician: test.tech@example.com / TestPass123
5. Verify dashboard muncul dengan booking data
```

### **Verify Database:**
```sql
-- Check users table
SELECT * FROM users WHERE role = 'technician';

-- Check technicians table
SELECT * FROM technicians;

-- Verify both records exist
SELECT u.name, u.email, t.phone, t.specialization 
FROM users u 
JOIN technicians t ON u.email = t.email 
WHERE u.role = 'technician';
```

---

## 🔗 Related Routes & Views

| Action | Route | View |
|--------|-------|------|
| **List Teknisi** | `GET /technicians` | `technicians.index` |
| **Tambah Teknisi** | `GET /technicians/create` | `technicians.create` |
| **Save Teknisi** | `POST /technicians` | *(Controller: store)* |
| **Lihat Detail** | `GET /technicians/{id}` | `technicians.show` |
| **Edit Teknisi** | `GET /technicians/{id}/edit` | `technicians.edit` |
| **Update Teknisi** | `PUT /technicians/{id}` | *(Controller: update)* |
| **Hapus Teknisi** | `DELETE /technicians/{id}` | *(Controller: destroy)* |
| **Login Teknisi** | `GET /login/technician` | `auth.login_technician` |
| **Proses Login** | `POST /login/technician` | *(Controller: loginTechnician)* |
| **Dashboard** | `GET /technician/dashboard` | `technician.dashboard` |

---

## 📞 Support & Troubleshooting

### **Technician tidak bisa login**
- [ ] Check email benar
- [ ] Check password benar (case-sensitive)
- [ ] Verify user record ada di `users` table
- [ ] Verify role = 'technician'
- [ ] Clear browser cache & cookies

### **Email conflict/duplicate**
- [ ] Check apakah email ada di `users` table
- [ ] Check apakah email ada di `technicians` table
- [ ] Gunakan email yang unik

### **Password tidak cocok**
- Check apakah user memasukkan password yang benar
- Password hashed di database, jangan di-copy langsung

**Untuk pertanyaan lebih lanjut**, hubungi admin sistem.
