<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 * SocialAuthController - OAuth/Social Login Integration
 * 
 * Handles authentication via social login providers (Google, Facebook, GitHub)
 * Using Laravel Socialite package untuk communicate dengan OAuth providers.
 * 
 * OAuth Flow:
 * 1. User click "Login with Google" button di login form
 * 2. Redirect ke redirectToProvider('google')
 * 3. User authenticate dengan Google (via browser redirect)
 * 4. Google callback ke our handleProviderCallback('google')
 * 5. App get OAuth user data dan process authentication
 * 6. User logged in dan redirect ke appropriate dashboard
 * 
 * Route Methods:
 * - redirectToProvider(string $provider): Redirect user ke OAuth provider
 * - handleProviderCallback(string $provider): Handle OAuth callback response
 * 
 * Supported Providers:
 * - google: Google OAuth authentication
 * - facebook: Facebook OAuth authentication
 * - github: GitHub OAuth authentication
 * 
 * Configuration:
 * - config/services.php: Define client_id, client_secret, redirect_url
 * - Example:
 *   'google' => [
 *       'client_id' => env('GOOGLE_CLIENT_ID'),
 *       'client_secret' => env('GOOGLE_CLIENT_SECRET'),
 *       'redirect' => env('GOOGLE_REDIRECT_URI'),
 *   ]
 * 
 * User Linking Strategy - User::findOrCreateFromOAuth():
 * 1. Check provider_id: Apakah user sudah login via provider ini sebelumnya?
 *    -> YES: Return existing user (update oauth_data)
 *    -> NO: Next step
 * 
 * 2. Check email: Apakah email dari OAuth ada di database?
 *    -> YES: Return existing user (link OAuth ke existing account)
 *    -> NO: Next step
 * 
 * 3. Create new: Buat user baru dengan data dari OAuth response
 *    Set: provider, provider_id, oauth_data, wasRecentlyCreated = true
 * 
 * New User Creation:
 * - Create User record dengan OAuth data
 * - Create Customer record (empty, user isi later di profile)
 * - Set role = 'customer' (default)
 * 
 * Security Measures:
 * 1. Provider Whitelist:
 *    - Only allow 'google', 'facebook', 'github'
 *    - Prevent injection attacks via provider parameter
 *    - Return error jika invalid provider
 * 
 * 2. Exception Handling:
 *    - Try-catch untuk OAuth redirect error
 *    - Try-catch untuk OAuth callback error
 *    - Log errors untuk debugging
 *    - Return user-friendly error message
 * 
 * 3. Unique Provider ID:
 *    - provider_id harus unique constraint di database
 *    - Prevent duplicate account linking
 * 
 * Error Scenarios:
 * - Invalid provider name -> Redirect to login with error
 * - OAuth configuration missing -> Redirect with config error
 * - User cancels OAuth consent -> Redirect with cancel error
 * - Network error during callback -> Redirect with error message
 * 
 * Redirect Behavior:
 * - Success: Redirect to appropriate dashboard
 *   - Admin: redirect('/dashboard') with admin dashboard
 *   - Customer: redirect('/customer-dashboard') with customer dashboard
 * 
 * - Error: Redirect to login page with error message:
 *   - "Provider tidak didukung"
 *   - "Gagal menghubungkan ke {Provider}"
 *   - "Gagal melakukan otentikasi dengan {Provider}"
 * 
 * Session & CSRF:
 * - Socialite handles session automatically
 * - CSRF token validation for security
 * - Stateless OAuth code grant flow
 * 
 * Integration Points:
 * - Triggered from: resources/views/auth/login.blade.php buttons
 * - Routes: route('social.redirect', $provider) & route('social.callback', $provider)
 * - Models: User::findOrCreateFromOAuth(), Customer
 * - Logging: \Log for error debugging
 */
class SocialAuthController extends Controller
{
    /**
     * Redirect user ke OAuth provider untuk authentication
     * 
     * Security:
     * - Validate provider whitelist (prevent injection)
     * - Only allow: google, facebook, github
     * - Reject unknown providers dengan error
     * 
     * Exception Handling:
     * - Catch any configuration or network errors
     * - Log error untuk debugging
     * - Return user-friendly error message
     * 
     * Process:
     * 1. Validate provider parameter
     * 2. Check provider configuration
     * 3. Socialite::driver($provider)->redirect()
     *    -> Generate OAuth authorization URL
     *    -> Store state token di session untuk security
     *    -> Redirect user ke OAuth provider
     * 
     * @param string $provider (google|facebook|github)
     * @return \Illuminate\Routing\Redirector | \Illuminate\View\View
     */
    public function redirectToProvider(string $provider)
    {
        // Whitelist providers untuk prevent injection attacks
        $allowedProviders = ['google', 'facebook', 'github'];
        
        // Validate provider is in whitelist
        if (!in_array($provider, $allowedProviders)) {
            return redirect()->route('login')->with('error', 'Provider tidak didukung');
        }

        try {
            // Redirect ke OAuth provider
            // Socialite automatically generate state token & save to session
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            // Catch config error, network error, etc
            \Log::error('OAuth Redirect Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal menghubungkan ke ' . ucfirst($provider) . '. Silakan cek konfigurasi.');
        }
    }

    /**
     * Handle OAuth provider callback response
     * 
     * Process:
     * 1. Get OAuth user data dari provider
     * 2. Find atau create User via findOrCreateFromOAuth()
     * 3. Create Customer record jika user baru
     * 4. Login user
     * 5. Redirect ke appropriate dashboard
     * 
     * OAuth User Data (dari provider):
     * - id: Unique identifier dari provider
     * - email: Email address
     * - name: Full name
     * - avatar: Profile picture URL
     * - etc (provider-specific fields)
     * 
     * Exception Handling:
     * - Handle user rejection (user denies consent)
     * - Handle network errors
     * - Handle invalid state token (CSRF protection)
     * - Log error untuk debugging
     * 
     * New User Workflow:
     * - User::findOrCreateFromOAuth() returns $user dengan wasRecentlyCreated=true
     * - Check flag via $user->wasRecentlyCreated
     * - Create associated Customer profile
     * - Set status message untuk user
     * 
     * Login & Redirect:
     * - Auth::login($user): Set user as authenticated
     * - Check $user->role untuk determine redirect destination:
     *   - admin: redirect to /dashboard
     *   - customer: redirect to /customer-dashboard
     * 
     * @param string $provider (google|facebook|github)
     * @return \Illuminate\Routing\Redirector
     */
    public function handleProviderCallback(string $provider)
    {
        try {
            // Get authenticated user data dari OAuth provider
            // Socialite validates state token automatically untuk CSRF protection
            $oauthUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            // Handle OAuth callback errors:
            // - User denied consent
            // - Invalid state token (CSRF)
            // - Network errors
            // - Provider errors
            \Log::error('OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal melakukan otentikasi dengan ' . ucfirst($provider));
        }

        // Find existing user atau create new
        // Strategy: check provider_id -> check email -> create new
        $user = User::findOrCreateFromOAuth($provider, $oauthUser);

        // Jika user baru (wasRecentlyCreated flag dari model)
        if ($user->wasRecentlyCreated) {
            // Create customer profile untuk new user
            Customer::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '', // User bisa update di profile nanti
                'address' => '',
            ]);
        }

        // Login user ke sistem
        Auth::login($user);

        // Redirect ke appropriate dashboard berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->route('dashboard')->with('success', 'Selamat datang Admin! Login via ' . ucfirst($provider));
        } else {
            return redirect()->route('customer.dashboard')->with('success', 'Selamat datang! Login via ' . ucfirst($provider));
        }
    }
}
