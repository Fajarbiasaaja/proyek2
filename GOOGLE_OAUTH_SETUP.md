# 🔧 Konfigurasi Google OAuth di JasaKu

## ❌ Masalah Saat Ini
Error: **"Missing required parameter: client_id"**

Penyebab: File `.env` tidak memiliki:
- `GOOGLE_CLIENT_ID` 
- `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI`

---

## ✅ Solusi: Setup Google OAuth

### STEP 1️⃣: Buat Google Cloud Project

1. Buka [Google Cloud Console](https://console.cloud.google.com)
2. **Create a new project**:
   - Click "Select a Project" (top left)
   - Click "NEW PROJECT"
   - Project name: `JasaKu` (atau nama lainnya)
   - Click "CREATE"

3. **Tunggu project dibuat** (~30 detik)

---

### STEP 2️⃣: Enable Google+ API

1. Di Google Cloud Console, search: `Google+ API`
2. Click "Google+ API" dalam hasil
3. Click "ENABLE"

Atau:
1. Click hamburger menu ☰ (top left)
2. Navigate to **APIs & Services** → **Library**
3. Search for **Google+ API**
4. Click **ENABLE**

---

### STEP 3️⃣: Buat OAuth 2.0 Credentials

1. Di **APIs & Services**, klik **Credentials** (sidebar kiri)
2. Click **+ CREATE CREDENTIALS** (tombol biru)
3. Pilih **OAuth client ID**
4. Jika diminta: **Create OAuth consent screen dulu**
   - Click **CONFIGURE CONSENT SCREEN**
   - Pilih **External** user type
   - Click **CREATE**
   
5. Isi **Consent Screen**:
   - **App name**: `JasaKu`
   - **User support email**: (email Anda)
   - **Developer contact**: (email Anda)
   - Click **SAVE AND CONTINUE**

6. **Scopes** (langsung SAVE AND CONTINUE)

7. **Test users** (langsung SAVE AND CONTINUE)

8. Kembali ke **Credentials**, click **+ CREATE CREDENTIALS** → **OAuth client ID**

---

### STEP 4️⃣: Setup OAuth Credential

Saat diminta untuk jenis aplikasi:
1. Pilih **Web application**
2. **Name**: `JasaKu Login`

3. **Authorized JavaScript origins**:
   ```
   http://localhost
   http://localhost:8000
   http://127.0.0.1
   http://127.0.0.1:8000
   ```
   Click **+ ADD URI** untuk masing-masing

4. **Authorized redirect URIs**:
   ```
   http://localhost/auth/google/callback
   http://localhost:8000/auth/google/callback
   http://127.0.0.1/auth/google/callback
   http://127.0.0.1:8000/auth/google/callback
   ```
   Click **+ ADD URI** untuk masing-masing

5. Click **CREATE**

---

### STEP 5️⃣: Copy Credentials

Setelah klik CREATE, popup akan menampilkan:
- **Client ID** (copy ini ✓)
- **Client secret** (copy ini ✓)

**SIMPAN DI TEMPAT AMAN!** Jangan share ke orang lain.

---

### STEP 6️⃣: Update `.env` File

Buka file `.env` di root project (c:\xampp\htdocs\proyek2\.env)

**Tambahkan di akhir file:**

```env
# ===== GOOGLE OAUTH =====
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID_DARI_GOOGLE
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET_DARI_GOOGLE
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# ===== FACEBOOK OAUTH (OPTIONAL) =====
# FACEBOOK_CLIENT_ID=your_facebook_client_id
# FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
# FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback

# ===== GITHUB OAUTH (OPTIONAL) =====
# GITHUB_CLIENT_ID=your_github_client_id
# GITHUB_CLIENT_SECRET=your_github_client_secret
# GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback
```

**Contoh (jangan copy, ganti dengan milik Anda):**
```env
GOOGLE_CLIENT_ID=123456789-abcdefg.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abcdefghijklmnop
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

### STEP 7️⃣: Verify Routes

Check file `routes/web.php` memiliki routes untuk OAuth:

```php
// Social Login Routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');
```

Jika tidak ada, tambahkan di file tersebut.

---

### STEP 8️⃣: Clear Cache & Test

1. **Clear Laravel cache:**
   ```bash
   php artisan config:cache
   ```

2. **Restart server Laravel:**
   - Stop server (Ctrl+C di terminal)
   - Run ulang: `php artisan serve`

3. **Test login:**
   - Buka http://localhost:8000/login
   - Click tombol "Login dengan Google"
   - Harusnya redirect ke Google login (bukan error)

---

## 🆘 Troubleshooting

### Error: "Unauthorized"
→ Credentials tidak sesuai di `.env`
→ Double-check `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET`

### Error: "Redirect URI mismatch"
→ Authorized redirect URIs di Google Cloud tidak sesuai
→ Update di Google Cloud Console

### Error: "client_id is missing"
→ `.env` belum ter-load
→ Run: `php artisan config:cache`
→ Restart server

### Tetap error setelah semua langkah?
1. Check `.env` file sudah tersimpan ✓
2. Check `config/services.php` punya Google config ✓
3. Check routes sudah ada ✓
4. Restart Apache/PHP server (xampp control panel)
5. Clear browser cache

---

## 📝 Login URLs untuk Testing

Setelah setup, gunakan email Google Anda untuk test:

- **Google Account**: akun Google pribadi Anda
- **Test Email**: lebih baik gunakan email real Google (bukan @gmail.test)

Jika Google account belum di-whitelist sebagai test user:
→ Go back ke **APIs & Services → OAuth consent screen**
→ Add email Anda sebagai test user
→ Save

---

## 🔒 Security Notes

- **Jangan commit `.env` ke Git** (sudah di .gitignore)
- **Jangan share `GOOGLE_CLIENT_SECRET`** di public
- **Update `GOOGLE_REDIRECT_URI`** untuk production:
  ```env
  GOOGLE_REDIRECT_URI=https://jasaku.com/auth/google/callback
  ```

---

## ✅ Verification Checklist

- [ ] Google Cloud project dibuat
- [ ] Google+ API di-enable
- [ ] OAuth consent screen di-configure
- [ ] OAuth credentials dibuat
- [ ] Client ID & Secret ter-copy
- [ ] `.env` file ter-update dengan credentials
- [ ] `php artisan config:cache` sudah dijalankan
- [ ] Server Laravel di-restart
- [ ] Routes ada di `routes/web.php`
- [ ] Tested login dengan Google account

---

## 📚 Dokumentasi Resmi

- [Google OAuth Setup](https://developers.google.com/identity/protocols/oauth2)
- [Laravel Socialite](https://laravel.com/docs/11.x/socialite)
- [JasaKu OAuth Implementation](./OAUTH_SETUP_GUIDE.md)

---

**Jika masih ada error, share screenshot error-nya untuk debugging lebih lanjut!** 🚀
