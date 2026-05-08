<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * ProfileController - User Account Management untuk Login User
 * 
 * Memungkinkan authenticated user (customer & admin) untuk manage profile mereka
 * seperti mengubah email, password, dan information personal.
 * 
 * Routes Methods:
 * - editEmail: Show form untuk change email
 * - updateEmail: Process email change dengan password verification
 * - editPassword: Show form untuk change password
 * - updatePassword: Process password change
 * - editProfile: Show form untuk edit profile (name, contact, address)
 * - updateProfile: Process profile update (different untuk customer vs admin)
 * 
 * Security Features:
 * 1. Password Verification:
 *    - Saat change email: harus verify current password
 *    - Reason: Email is account identifier, perlu strong verification
 * 
 * 2. Password Change:
 *    - Must provide current password untuk verify identity
 *    - New password harus confirmed (password_confirmed)
 *    - Min 8 characters untuk security strength
 * 
 * 3. Email Uniqueness:
 *    - New email harus unique di users table (tidak boleh duplicate)
 *    - Rule::unique('users')->ignore($user->id) untuk ignore current user
 * 
 * Role-Based Profile Edit:
 * - Admin: Edit name only (basic staff info)
 * - Customer: Edit name + contact fields (phone, address, city, postal_code)
 * 
 * Customer Profile Update:
 * - Update both User (name) dan Customer (full profile)
 * - Find customer via email address matching
 * - Update customer address fields untuk service location
 * 
 * Authorization:
 * - Semua methods protected dengan 'auth' middleware
 * - User hanya bisa edit own profile (Auth::user())
 * - Tidak ada super-admin override (security)
 * 
 * Validasi & Error Messages:
 * - Custom error messages dalam bahasa Indonesia
 * - Email unique validation: "Email ini sudah digunakan oleh user lain"
 * - Password validation: "Password tidak sesuai" untuk incorrect password
 * 
 * Password Hashing:
 * - Update password harus Hash::make() sebelum save
 * - Verify dengan Hash::check($plain, $hashed)
 * - Tidak boleh store plain text password!
 * 
 * Redirect Behavior:
 * - Success: redirect()->back()->with('success', message)
 * - Error: back()->withErrors(['field' => 'message'])
 * - All forms back ke same page (for inline editing)
 */
class ProfileController extends Controller
{
    /**
     * Show form untuk change email address
     * 
     * Pass current user ke view untuk display existing email
     * 
     * @return \Illuminate\View\View
     */
    public function editEmail()
    {
        // Get current authenticated user
        $user = Auth::user();
        return view('profile.edit-email', compact('user'));
    }

    /**
     * Process email change dengan password verification
     * 
     * Validasi:
     * - email: required, email format, unique (except current user)
     * - password: required (untuk verify user identity)
     * 
     * Custom Error Messages:
     * - email.unique: "Email ini sudah digunakan oleh user lain"
     * - password.required: "Password diperlukan untuk mengubah email"
     * 
     * Logic:
     * 1. Validasi input
     * 2. Hash::check() untuk verify password sebelum allow change
     * 3. Jika password tidak match -> return error
     * 4. Update email address
     * 5. Redirect back dengan success message
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEmail(Request $request)
    {
        // Get current user
        $user = Auth::user();

        // Validasi input dengan custom messages
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id), // Ignore current user
            ],
            'password' => 'required',
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh user lain.',
            'password.required' => 'Password diperlukan untuk mengubah email.',
        ]);

        // Verify password sebelum allow change
        if (!Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['password' => 'Password tidak sesuai.']);
        }

        // Update email address
        $user->update(['email' => $validated['email']]);

        return redirect()->back()->with('success', 'Email berhasil diubah menjadi ' . $validated['email']);
    }

    /**
     * Show form untuk change password
     * 
     * @return \Illuminate\View\View
     */
    public function editPassword()
    {
        return view('profile.edit-password');
    }

    /**
     * Process password change
     * 
     * Validasi:
     * - current_password: required (untuk verify user identity)
     * - password: required, min 8 chars, must be confirmed
     *   confirmed rule: check password === password_confirmation
     * 
     * Custom Error Messages (dalam Bahasa Indonesia):
     * - current_password.required: "Password saat ini diperlukan"
     * - password.required: "Password baru diperlukan"
     * - password.min: "Password baru minimal 8 karakter"
     * - password.confirmed: "Konfirmasi password tidak sesuai"
     * 
     * Logic:
     * 1. Validasi input
     * 2. Hash::check() current password (verify user identity)
     * 3. Jika tidak match -> return error
     * 4. Hash::make() new password sebelum save
     * 5. Update user record
     * 6. Redirect back dengan success message
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        // Get current user
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini diperlukan.',
            'password.required' => 'Password baru diperlukan.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        // Verify current password sebelum allow change
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // Update password (with hashing)
        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Show form untuk edit profile (name, contact, address)
     * 
     * Different form untuk customer vs admin:
     * - Admin form: name only (basic staff profile)
     * - Customer form: name + phone + address + city + postal_code
     * 
     * @return \Illuminate\View\View
     */
    public function editProfile()
    {
        // Get current user
        $user = Auth::user();
        
        // Check role untuk determine form type
        if ($user->role === 'customer') {
            // Load customer profile untuk show current data
            $customer = \App\Models\Customer::where('email', $user->email)->first();
            return view('profile.edit-profile-customer', compact('user', 'customer'));
        } else {
            // Admin form (simple)
            return view('profile.edit-profile-admin', compact('user'));
        }
    }

    /**
     * Process profile update
     * 
     * Different validation rules untuk customer vs admin:
     * 
     * Admin:
     * - name: required
     * - phone, address, city, postal_code: nullable
     * 
     * Customer:
     * - name: required
     * - phone: required
     * - address: required (service address)
     * - city, postal_code: optional
     * 
     * Logic:
     * 1. Validasi input (role-specific)
     * 2. Update User record (name)
     * 3. Jika customer: update Customer record juga (all address fields)
     * 4. Redirect back dengan success message
     * 
     * Dual Update Pattern:
     * - User table punya name (for login display)
     * - Customer table punya complete profile + address (for service)
     * - Keep both in sync untuk consistency
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        // Get current user
        $user = Auth::user();

        // Role-specific validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => $user->role === 'customer' ? 'required|string|max:20' : 'nullable',
            'address' => $user->role === 'customer' ? 'required|string' : 'nullable',
            'city' => $user->role === 'customer' ? 'nullable|string' : 'nullable',
            'postal_code' => $user->role === 'customer' ? 'nullable|string|max:20' : 'nullable',
        ]);

        // Update User table (name)
        $user->update(['name' => $validated['name']]);

        // Update Customer table jika customer role
        if ($user->role === 'customer') {
            // Find customer by email (link to user)
            $customer = \App\Models\Customer::where('email', $user->email)->first();
            if ($customer) {
                // Update customer address fields
                $customer->update([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'city' => $validated['city'] ?? $customer->city,
                    'postal_code' => $validated['postal_code'] ?? $customer->postal_code,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Profil berhasil diubah.');
    }
}
