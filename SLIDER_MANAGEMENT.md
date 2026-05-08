# 🎡 Dashboard Slider Management - Documentation

## Fitur Yang Ditambahkan

Sistem slider otomatis di dashboard yang bisa di-manage dari admin panel dengan fitur:

✅ **Auto-sliding carousel** - Gambar berganti otomatis setiap 5 detik  
✅ **Responsive design** - Optimal di berbagai ukuran layar  
✅ **Admin management** - Add, edit, delete, dan enable/disable slider  
✅ **Image upload** - Support JPG, PNG, GIF, WebP (max 5MB)  
✅ **Call-to-action buttons** - Tombol dengan custom text dan link  
✅ **Drag-and-drop ordering** - Atur urutan penampilan slider  
✅ **Live preview** - Preview gambar sebelum upload  

---

## 📁 Files yang Ditambahkan

### Database & Model
- `database/migrations/2025_02_18_110000_create_sliders_table.php` - Migration untuk tabel sliders
- `app/Models/Slider.php` - Model dengan helper methods

### Controller
- `app/Http/Controllers/SliderController.php` - CRUD dan management

### Views
- `resources/views/admin/sliders/index.blade.php` - List sliders
- `resources/views/admin/sliders/create.blade.php` - Form tambah slider
- `resources/views/admin/sliders/edit.blade.php` - Form edit slider

### Updated Files
- `resources/views/dashboard.blade.php` - Menampilkan slider carousel
- `app/Http/Controllers/DashboardController.php` - Pass sliders ke view

---

## 🚀 Cara Penggunaan

### 1. Setup Database

Jalankan migration untuk membuat tabel sliders:
```bash
php artisan migrate
```

Tabel `sliders` akan dibuat dengan struktur:
```sql
- id (primary key)
- title (optional)
- description (optional)
- image (required)
- button_text (optional)
- button_link (optional)
- sort_order (untuk urutan tampil)
- is_active (status aktif/nonaktif)
- timestamps
```

### 2. Admin Panel - Manage Sliders

**URL:** `http://localhost:8000/sliders`

#### Tambah Slider Baru
1. Klik tombol **"+ Tambah Slider"**
2. Upload gambar (rekomendasi: 1920 x 500px)
3. Isi judul, deskripsi (optional)
4. Isi teks dan link tombol (optional)
5. Atur urutan (sort order)
6. Centang "Aktifkan Slider"
7. Klik **"Simpan Slider"**

#### Edit Slider
1. Klik tombol ✏️ pada slider yang ingin diedit
2. Ubah data yang diperlukan
3. Upload gambar baru (atau biarkan kosong untuk keep current)
4. Klik **"Simpan Perubahan"**

#### Delete Slider
1. Klik tombol 🗑️ pada slider
2. Konfirmasi delete
3. Gambar akan dihapus otomatis dari storage

#### Toggle Status
1. Klik tombol 👁️ untuk enable/disable slider
2. Slider nonaktif tidak akan ditampilkan di dashboard

---

## 🎨 Slider Display

### Dashboard
- Sliders ditampilkan di deck slider carousel
- Auto-slide setiap 5 detik
- Support manual navigation (prev/next buttons)
- Indikator dots untuk slide counter
- Efek fade transition yang smooth

### Features
- **Title** - Ditampilkan besar di slider
- **Description** - Subtitle/deskripsi
- **Button** - Call-to-action dengan link (bisa internal atau external)
- **Image** - Full-width background

---

## 📋 Database Structure

```php
Schema::create('sliders', function (Blueprint $table) {
    $table->id();
    $table->string('title')->nullable();
    $table->longText('description')->nullable();
    $table->string('image');                    // Stored in storage/app/public/sliders/
    $table->string('button_text')->nullable();
    $table->string('button_link')->nullable(); 
    $table->integer('sort_order')->default(0);  // Urutan display
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

## 🔧 API Endpoints

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/sliders` | List semua slider |
| GET | `/sliders/create` | Form tambah slider |
| POST | `/sliders` | Store slider baru |
| GET | `/sliders/{id}/edit` | Form edit slider |
| PUT | `/sliders/{id}` | Update slider |
| DELETE | `/sliders/{id}` | Delete slider |
| POST | `/sliders/{id}/toggle-active` | Toggle aktif/nonaktif |

---

## 💻 Code Examples

### Get Active Sliders (di Controller/View)
```php
use App\Models\Slider;

// Get semua slider yang aktif (sorted by sort_order)
$sliders = Slider::getActive();

// atau query manual
$sliders = Slider::where('is_active', true)
    ->orderBy('sort_order', 'asc')
    ->get();
```

### Delete Image Otomatis
Image file otomatis dihapus dari storage saat slider dihapus (di Model).

---

## 🎯 Rekomendasi

### Ukuran Gambar
- **Optimal:** 1920 x 500 px
- **Format:** JPG, PNG > WebP (untuk size lebih kecil)
- **Max size:** 5MB
- **Color profile:** RGB

### Tips Design
1. Gunakan gambar berkualitas tinggi
2. Sesuaikan dengan brand/tema AC
3. Tambahkan text overlay yang jelas
4. Maksimal 5-7 slider aktif untuk performa
5. Ganti slider setiap bulan untuk fresh content

### Link Button
- Bisa ke halaman internal: `/services`, `/bookings`
- Bisa ke external: `https://example.com`
- Kosongkan jika tidak perlu button

---

## 🐛 Troubleshooting

### Slider tidak muncul di dashboard
**Solusi:**
1. Pastikan ada slider dengan `is_active = true`
2. Check migration sudah dijalankan: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`

### Gambar tidak tampil
**Solusi:**
1. Pastikan storage sudah di-link: `php artisan storage:link`
2. Check file ada di `storage/app/public/sliders/`
3. Check permission folder (755)

### Carousel tidak auto-slide
**Solusi:**
1. Bootstrap JS harus di-load
2. Check browser console untuk error
3. Verifikasi `data-bs-ride="carousel"` di HTML

### Upload gambar error
**Solusi:**
1. Check file size < 5MB
2. Pastikan format: JPG/PNG/GIF/WebP
3. Check folder permission `storage/app/public/sliders/`

---

## 🔐 Security

- ✅ File upload validated (mimes + size)
- ✅ Image stored di public storage
- ✅ Image auto-deleted saat slider dihapus
- ✅ Only admin dapat manage slider
- ✅ CSRF protection di forms
- ✅ Unauthorized access protected

---

## 📊 Performance

- Sliders cached di query (query optimization)
- Images optimized untuk web
- Fade transitions (smooth animation)
- Responsive dan mobile-friendly
- Lazy loading untuk images

---

## 🎬 Setup Chart

```
Database Migration
    ↓
Create Slider Model
    ↓
Create SliderController
    ↓
Create Admin Views
    ↓
Update Dashboard
    ↓
Add Routes
    ↓
Ready to Use!
```

---

## 📚 Related Files

- Update: `routes/web.php` - Routes untuk slider management
- Update: `app/Http/Controllers/DashboardController.php` - Pass data ke dashboard
- Update: `resources/views/dashboard.blade.php` - Menampilkan carousel

---

## ✨ Next Steps (Optional)

Fitur yang bisa ditambahkan di masa depan:
- [ ] Slider scheduling (auto activate/deactivate by date)
- [ ] Analytics (track clicks per slider)
- [ ] Template builder untuk slider content
- [ ] Video slider support
- [ ] Animation effects selector

---

**Slider Management Ready!** 🎉

Untuk mulai menggunakan:
1. Run migration: `php artisan migrate`
2. Buka: `http://localhost:8000/sliders`
3. Tambah slider pertama Anda
4. Lihat hasilnya di dashboard!
