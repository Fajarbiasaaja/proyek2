# ⚡ Quick Start: Social Login Setup

Sistem login dengan Google, Facebook, dan GitHub sudah terintegrasi! Berikut adalah langkah cepat untuk menjalankannya.

## 🚀 Quick Setup (5 Langkah)

### 1. Start XAMPP
- Buka XAMPP Control Panel
- Klik **Start** pada MySQL dan Apache

### 2. Setup Credentials

#### Google
- Buka: https://console.cloud.google.com/
- Buat OAuth 2.0 Credentials
- Copy Client ID & Secret ke `.env`:
```env
GOOGLE_CLIENT_ID=your_id
GOOGLE_CLIENT_SECRET=your_secret
```

#### Facebook  
- Buka: https://developers.facebook.com/
- Buat App dan dapatkan App ID & Secret
- Copy ke `.env`:
```env
FACEBOOK_CLIENT_ID=your_id
FACEBOOK_CLIENT_SECRET=your_secret
```

#### GitHub
- Buka: https://github.com/settings/developers
- Buat OAuth App
- Copy Client ID & Secret ke `.env`:
```env
GITHUB_CLIENT_ID=your_id
GITHUB_CLIENT_SECRET=your_secret
```

### 3. Run Migration
```bash
php artisan migrate
```

### 4. Start Server
```bash
php artisan serve
```

### 5. Test
- Buka: http://localhost:8000/login
- Klik tombol Google, Facebook, atau GitHub
- Selesai! ✅

## 📚 Full Setup Guide
Lihat file **SOCIAL_AUTH_SETUP.md** untuk panduan lengkap dan troubleshooting.

## 🎯 Fitur yang Tersedia
✅ Login dengan Google  
✅ Login dengan Facebook  
✅ Login dengan GitHub  
✅ Auto user creation  
✅ Linking existing accounts  
✅ Role-based redirect (admin/customer)  

## ❓ Common Issues

**"Credentials kosong"** → Isi `.env` dengan credentials dari provider  
**"MySQL tidak running"** → Start MySQL di XAMPP Control Panel  
**"Social buttons tidak keluar"** → Jalankan `php artisan config:cache`  

---
Dokumentasi lengkap tersedia di **SOCIAL_AUTH_SETUP.md**
