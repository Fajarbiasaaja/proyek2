<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * AuthController
 * 
 * Controller untuk menangani authentication dan authorization:
 * - Login (email + password)
 * - Register (self-service pelanggan)
 * - Logout
 * 
 * Routes:
 * - GET /login -> showLogin()
 * - POST /login -> login()
 * - GET /register -> showRegister()
 * - POST /register -> register()
 * - POST /logout -> logout()
 * 
 * Note: Social login (OAuth) ditangani di SocialAuthController
 */
class AuthController extends Controller
{
    /**
     * Show login form
     * 
     * Menampilkan halaman login untuk user masuk ke sistem.
     * User dapat login dengan email+password atau via OAuth.
     * 
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show technician login form
     * 
     * Menampilkan halaman login khusus untuk teknisi.
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginTechnician()
    {
        return view('auth.login_technician');
    }

    /**
     * Handle user login
     * 
     * Flow:
     * 1. Validasi email dan password
     * 2. Attempt login menggunakan credentials
     * 3. Regenerate session untuk security
     * 4. Redirect ke dashboard sesuai role:
     *    - admin -> /dashboard
     *    - customer -> /customer/dashboard
     * 
     * @param Request $request Input dari login form
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt login menggunakan email dan password
        if (Auth::attempt($validated)) {
            // Regenerate session untuk security (prevent session fixation)
            $request->session()->regenerate();
            
            // Redirect ke dashboard sesuai role user
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard')->with('success', 'Selamat datang Admin!');
            } else {
                return redirect()->route('customer.dashboard')->with('success', 'Selamat datang!');
            }
        }

        // Login gagal - return ke form dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle technician login
     * 
     * Flow:
     * 1. Validasi email dan password
     * 2. Attempt login dengan credentials tersebut
     * 3. Validasi bahwa user memiliki role 'technician'
     * 4. Regenerate session untuk security
     * 5. Redirect ke technician dashboard
     * 
     * @param Request $request Input dari login form technician
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginTechnician(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt login menggunakan email dan password
        if (Auth::attempt($validated)) {
            $user = Auth::user();
            
            // Validasi bahwa user adalah technician
            if ($user->role !== 'technician') {
                // Logout jika role bukan technician
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun ini bukan akun teknisi. Silakan gunakan login reguler.',
                ])->onlyInput('email');
            }
            
            // Regenerate session untuk security
            $request->session()->regenerate();
            
            // Redirect ke technician dashboard
            return redirect()->route('technician.dashboard')->with('success', 'Selamat datang, Teknisi!');
        }

        // Login gagal - return ke form dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     * 
     * Menampilkan halaman pendaftaran untuk customer baru.
     * 
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Show provider (technician) registration form
     */
    public function showProviderRegister()
    {
        return view('auth.register_provider');
    }

    /**
     * Handle user registration
     * 
     * Flow:
     * 1. Validasi input (name, email, password, phone, address)
     * 2. Create user di tabel users dengan role 'customer'
     * 3. Create corresponding customer record di tabel customers
     * 4. Auto-login setelah registrasi
     * 5. Redirect ke customer dashboard
     * 
     * @param Request $request Input dari registration form
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validasi input registrasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users', // Email harus unique
            'password' => 'required|min:8|confirmed', // Harus confirm password
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        // Step 1: Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Hash password
            'role' => 'customer', // Default role adalah customer
        ]);

        // Step 2: Create corresponding customer record
        // Ini untuk keperluan data operasional (phone, address untuk booking)
        \App\Models\Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        // Step 3: Auto-login user yang baru registrasi
        Auth::login($user);

        // Step 4: Redirect ke dashboard dengan success message
        return redirect()->route('customer.dashboard')->with('success', 'Registrasi berhasil! Selamat datang!');
    }

    /**
     * Handle provider (technician) registration
     * Membuat akun user dengan role 'technician' dan record di tabel technicians
     */
    public function registerProvider(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'specialization' => 'nullable|string|max:255',
        ]);

        // Create user with role technician
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'technician',
        ]);

        // Create technician profile
        Technician::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'specialization' => $validated['specialization'] ?? null,
            'status' => 'available',
        ]);

        // Option: auto-login the new provider and redirect to home
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registrasi penyedia jasa berhasil. Selamat datang!');
    }

    /**
     * Handle user logout
     * 
     * Flow:
     * 1. Logout user
     * 2. Invalidate session (habiskan session)
     * 3. Regenerate CSRF token untuk security
     * 4. Redirect ke login page
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Logout user
        Auth::logout();
        
        // Invalidate session - hapus semua session data
        $request->session()->invalidate();
        
        // Regenerate CSRF token untuk security
        $request->session()->regenerateToken();
        
        // Redirect ke login dengan message
        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
