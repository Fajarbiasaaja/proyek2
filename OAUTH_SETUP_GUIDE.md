# OAuth Setup Guide - Google, Facebook, GitHub

## Permasalahan
Error "Missing required parameter: redirect_uri" berarti OAuth credentials belum dikonfigurasi.

---

## Setup Google OAuth (Recommended)

### Step 1: Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click "Create Project"
3. Enter project name: `AC Server Application` atau bebas
4. Click "Create"

### Step 2: Enable Google+ API
1. In Google Cloud Console, go to **APIs & Services** > **Library**
2. Search for "Google+ API"
3. Click on it and press "Enable"

### Step 3: Create OAuth Credentials
1. Go to **APIs & Services** > **Credentials**
2. Click "Create Credentials" > "OAuth client ID"
3. Choose "Web application"
4. Set Application name: `AC Service Login`
5. Under "Authorized redirect URIs", add:
   ```
   http://127.0.0.1:8000/login/google/callback
   http://localhost:8000/login/google/callback
   http://your-domain.com/login/google/callback
   ```
   (Add all URLs where app will be accessible)

### Step 4: Copy Credentials
You'll see a popup with:
- **Client ID** 
- **Client Secret**

Copy both values

### Step 5: Add to .env
Edit `.env` file in project root:

```env
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID_HERE
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET_HERE
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/login/google/callback
```

### Step 6: Test Login
1. Go to http://127.0.0.1:8000/login
2. Click "Sign in with Google" button
3. You should now be able to login!

---

## Setup Facebook OAuth (Optional)

### Step 1: Create Facebook App
1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Click "My Apps" > "Create App"
3. Choose app type: "Consumer"
4. Fill in app details

### Step 2: Configure Facebook Login
1. In app dashboard, click "Add Product"
2. Find "Facebook Login" and click "Set Up"
3. Choose "Web"

### Step 3: Add OAuth Redirect URI
1. Go to Settings > Basic
2. Under "App Domains", add your domain
3. Go to Facebook Login > Settings
4. Under "Valid OAuth Redirect URIs", add:
   ```
   http://127.0.0.1:8000/login/facebook/callback
   ```

### Step 4: Get Credentials
1. Go to Settings > Basic
2. Copy:
   - **App ID** (use as FACEBOOK_CLIENT_ID)
   - **App Secret** (use as FACEBOOK_CLIENT_SECRET)

### Step 5: Add to .env
```env
FACEBOOK_CLIENT_ID=YOUR_APP_ID_HERE
FACEBOOK_CLIENT_SECRET=YOUR_APP_SECRET_HERE
FACEBOOK_REDIRECT_URI=http://127.0.0.1:8000/login/facebook/callback
```

---

## Setup GitHub OAuth (Optional)

### Step 1: Register New OAuth App
1. Go to GitHub
2. Settings > Developer settings > OAuth Apps
3. Click "New OAuth App"

### Step 2: Fill Application Details
- Application name: `AC Service`
- Homepage URL: `http://127.0.0.1:8000`
- Authorization callback URL: `http://127.0.0.1:8000/login/github/callback`
- Click "Register application"

### Step 3: Get Credentials
You'll see:
- **Client ID**
- **Client Secret**

Copy both values (if secret not visible, click "Generate a new client secret")

### Step 4: Add to .env
```env
GITHUB_CLIENT_ID=YOUR_CLIENT_ID_HERE
GITHUB_CLIENT_SECRET=YOUR_CLIENT_SECRET_HERE
GITHUB_REDIRECT_URI=http://127.0.0.1:8000/login/github/callback
```

---

## Testing OAuth Login

### Login Flow
1. Open http://127.0.0.1:8000/login
2. Click "Sign in with [Provider]" button
3. You'll be redirected to provider's login
4. Authorize the application
5. Redirected back to app
6. You're logged in!

### If Still Getting Error

**Error: "Missing required parameter: redirect_uri"**
- Check GOOGLE_REDIRECT_URI in .env matches config/services.php
- Make sure URL is exactly correct (http/https, domain, path)

**Error: "Socialite not configured"**
- Likely Provider credentials missing or wrong in .env*

**Error: "The HTTP request went away"**
- Make sure allow external requests in your network/firewall

---

## Troubleshooting

### Check Current Configuration
Run in terminal:
```bash
php artisan tinker
>>> config('services.google')
```

This shows what Laravel sees in config. Should match your .env values.

### Debug OAuth Flow
Add logging to SocialAuthController.php to see errors:
```php
\Log::info('OAuth attempt', [
    'provider' => $provider,
    'redirect_uri' => config('services.' . $provider . '.redirect'),
]);
```

### If Credentials Not Working
1. Double-check Client ID and Secret are exactly copied
2. Verify redirect_uri matches EXACTLY in provider's settings
3. Make sure app is in correct environment (development vs production)
4. Try creating new credentials fresh

---

## Environment Variations

### For Different Domains

**Local Development:**
```env
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/login/google/callback
```

**Production (HTTPS):**
```env
GOOGLE_REDIRECT_URI=https://yourdomain.com/login/google/callback
```

### Multiple Redirect URIs
Add multiple URLs in provider credentials settings (not in .env).

The .env only needs ONE primary redirect_uri which Laravel will use.

---

## Quick Test Without Real Credentials

If you just want to test without OAuth (for now):

1. Comment out OAuth buttons in `resources/views/auth/login.blade.php`
2. Use email/password login to test other features
3. Add OAuth credentials later when ready

---

## File Locations to Reference

- Config: `config/services.php`
- Controller: `app/Http/Controllers/SocialAuthController.php`
- Routes: `routes/web.php` (lines with SocialAuthController)
- Environment: `.env` file in project root

---

For more help: Check Laravel Socialite documentation at https://laravel.com/docs/socialite
