# ⚡ Quick Start: Dashboard Slider

## 3 Langkah Mudah

### 1️⃣ Migration Database
```bash
cd C:\xampp\htdocs\proyek2
php artisan migrate
```

### 2️⃣ Akses Slider Management
Buka browser: **`http://localhost:8000/sliders`**

### 3️⃣ Tambah Slider Pertama
1. Klik **"+ Tambah Slider"**
2. Upload gambar (1920x500px recommended)
3. Isi judul & deskripsi (optional)
4. Tambah tombol dengan text & link (optional)
5. Centang "Aktifkan Slider"
6. Klik **"Simpan"**

---

## 🎡 Lihat Hasilnya

1. Go to dashboard: `http://localhost:8000/dashboard`
2. Slider carousel otomatis muncul di atas!
3. Gambar berganti otomatis setiap 5 detik
4. Bisa drag kiri/kanan untuk manual
5. Indikator dots untuk slide counter

---

## 📝 Form Fields

| Field | Required | Notes |
|-------|----------|-------|
| Gambar | ✅ Yes | Max 5MB, JPG/PNG/GIF/WebP |
| Judul | ❌ No | Tampil besar di slider |
| Deskripsi | ❌ No | Subtitle/penjelasan |
| Teks Tombol | ❌ No | Requires button link too |
| Link Tombol | ❌ No | URL internal atau external |
| Urutan | ❌ No | Angka kecil = tampil duluan |
| Aktifkan | ❌ No | Centang untuk show di dashboard |

---

## 📁 Files Added

- Migration: `create_sliders_table.php`
- Model: `app/Models/Slider.php`
- Controller: `app/Http/Controllers/SliderController.php`
- Views: `resources/views/admin/sliders/{index,create,edit}.blade.php`

---

## 🎨 Slider Features

✅ Auto-slide (5 seconds)  
✅ Fade transitions  
✅ Navigation buttons  
✅ Slide indicators  
✅ Responsive design  
✅ Call-to-action buttons  
✅ Mobile friendly  

---

## 🔧 Admin Menu

Lokasi menu sudah ada di admin navigation. Cari menu "Sliders" atau akses langsung via:
- **List:** `/sliders`
- **Create:** `/sliders/create`
- **Edit:** `/sliders/{id}/edit`

---

## 💡 Tips

- Gambar optimal: **1920 x 500 px**
- Format terbaik: **WebP** (kecil, cepat)
- Judul + deskripsi = engagement lebih tinggi
- Maksimal 5-7 slider untuk performa
- Ganti content bulanan untuk fresh look

---

## 📚 Full Documentation

Lihat file [SLIDER_MANAGEMENT.md](SLIDER_MANAGEMENT.md) untuk dokumentasi lengkap!

---

**Ready to add sliders?** 🚀

Mulai dengan: `http://localhost:8000/sliders`
