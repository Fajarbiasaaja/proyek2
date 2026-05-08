<?php

/**
 * ===== WEB ROUTES =====
 * 
 * File ini mendefinisikan semua HTTP routes untuk aplikasi Jasa Servis AC.
 * Routes diorganisir berdasarkan middleware dan role untuk clarity dan security.
 * 
 * STRUKTUR ROUTES:
 * 
 * 1. PUBLIC ROUTES
 *    - Landing page (/)
 *    - Authentication (login, register, logout)
 *    - OAuth callback (Google, Facebook, GitHub)
 * 
 * 2. AUTHENTICATED ROUTES (requires auth)
 *    - Profile management (edit email, password, profile)
 * 
 * 3. ADMIN ROUTES (requires auth + admin role)
 *    - Dashboard (/dashboard)
 *    - CRUD resources: customers, technicians, services, bookings, invoices
 *    - Slider management untuk homepage carousel
 *    - Special actions: booking cancel/complete, invoice mark-paid, slider toggle
 * 
 * 4. CUSTOMER ROUTES (requires auth + customer role)
 *    - Customer dashboard (/customer/dashboard)
 *    - Customer-specific operations
 * 
 * MIDDLEWARE NOTES:
 * - 'guest': Route hanya untuk non-authenticated users
 * - 'auth': Route hanya untuk authenticated users
 * - 'admin': Route hanya untuk users dengan role 'admin'
 * - 'customer': Route hanya untuk users dengan role 'customer'
 * 
 * CONVENTION:
 * - RESTful resources menggunakan Route::resource()
 * - Custom actions menggunakan named routes dengan format: resource.action
 * - Prefix customer routes (/customer) untuk separation
 */

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Http\Controllers\TechnicianBookingController;
use App\Http\Controllers\TechnicianTaskController;
use App\Http\Controllers\TechnicianProfileController;
use App\Http\Controllers\TechnicianEarningsController;
use App\Http\Controllers\TechnicianRatingController;
use Illuminate\Support\Facades\Route;

// ============================================
// 1. PUBLIC ROUTES (Landing & Authentication)
// ============================================

/**
 * Root route (/)
 * 
 * Smart redirect berdasarkan auth status:
 * - If authenticated:
 *   - Admin -> /dashboard
 *   - Customer -> /customer/dashboard
 * - If not authenticated -> /welcome page
 */
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        } elseif (auth()->user()->role === 'technician') {
            return redirect()->route('technician.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }
    return view('welcome');
})->name('home');

// Test payment click (temporary for debugging)
Route::get('/test-payment-click', function () {
    return view('test-payment-click');
})->name('test-payment-click');

/**
 * Authentication Routes (untuk guest users only)
 * 
 * Termasuk:
 * - Login (email + password)
 * - Register (self-service customer)
 * - OAuth login (Google, Facebook, GitHub)
 */
Route::middleware('guest')->group(function () {
    // Traditional authentication
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    
    // Technician authentication
    Route::get('/login/technician', [AuthController::class, 'showLoginTechnician'])->name('login.technician');
    Route::post('/login/technician', [AuthController::class, 'loginTechnician'])->name('technician.login');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    // Pendaftaran khusus untuk penyedia jasa / teknisi
    Route::get('/register/provider', [AuthController::class, 'showProviderRegister'])->name('register.provider');
    Route::post('/register/provider', [AuthController::class, 'registerProvider'])->name('auth.register.provider');
    
    // OAuth authentication (Socialite)
    // Providers: google, facebook, github
    // Flow: /login/{provider} -> redirect ke OAuth provider -> /login/{provider}/callback
    Route::get('/login/{provider}', [SocialAuthController::class, 'redirectToProvider'])
        ->name('social.redirect')
        ->where('provider', 'google|facebook|github');
    Route::get('/login/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
        ->name('social.callback')
        ->where('provider', 'google|facebook|github');
});

/**
 * Logout Route
 * 
 * POST endpoint untuk logout (destroy session)
 * Available untuk semua authenticated users
 */
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===== PUBLIC API ROUTES =====
/**
 * Technician Ratings - Public routes untuk view ratings
 * (accessible tanpa authentication)
 */
Route::get('/technicians/{technician}/ratings', [RatingController::class, 'getTechnicianRatings'])->name('ratings.technician');
Route::get('/technicians/top-rated', [RatingController::class, 'getTopRatedTechnicians'])->name('ratings.topRated');

// ============================================
// 2. AUTHENTICATED ROUTES (untuk semua auth users)
// ============================================

/**
 * Profile Management Routes
 * 
 * User dapat manage profile mereka:
 * - Edit email
 * - Change password
 * - Edit profile data
 */
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit-email', [ProfileController::class, 'editEmail'])->name('profile.editEmail');
    Route::put('/profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.updateEmail');
    Route::get('/profile/edit-password', [ProfileController::class, 'editPassword'])->name('profile.editPassword');
    Route::put('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::get('/profile/edit', [ProfileController::class, 'editProfile'])->name('profile.editProfile');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');
});

// ============================================
// 3. ADMIN ROUTES (requires auth + admin role)
// ============================================

/**
 * Admin Routes
 * 
 * Middleware: auth + admin
 * Hanya accessible oleh users dengan role = 'admin'
 * 
 * Termasuk:
 * - Dashboard (overview & statistics)
 * - CRUD resources (customers, technicians, services, bookings, invoices)
 * - Slider management untuk homepage carousel
 * - Custom actions untuk update status
 */
Route::middleware(['auth', 'admin'])->group(function () {
    
    // ===== DASHBOARD =====
    // Overview page dengan statistics dan recent data
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== CUSTOMER MANAGEMENT =====
    // CRUD operations untuk customer data
    Route::resource('customers', CustomerController::class);

    // ===== TECHNICIAN MANAGEMENT =====
    // CRUD operations untuk technician data
    Route::resource('technicians', TechnicianController::class);

    // ===== SERVICE MANAGEMENT =====
    // CRUD operations untuk service/layanan data
    Route::resource('services', ServiceController::class);

    // ===== BOOKING MANAGEMENT =====
    // CRUD operations untuk booking/pemesanan
    Route::resource('bookings', BookingController::class);
    
    // Custom booking actions
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/mark-completed', [BookingController::class, 'markAsCompleted'])->name('bookings.markAsCompleted');

    // ===== INVOICE MANAGEMENT =====
    // CRUD operations untuk invoice/tagihan (read-only mostly)
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    
    // Custom invoice actions
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markAsPaid');

    // ===== PAYMENT MANAGEMENT =====
    // Admin approval workflow untuk payment transactions
    Route::get('/payments/pending', [PaymentController::class, 'listPendingPayments'])->name('payments.pending');
    Route::get('/payments/{payment}', [PaymentController::class, 'paymentDetail'])->name('payments.show');
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approvePayment'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'rejectPayment'])->name('payments.reject');

    // ===== SLIDER/CAROUSEL MANAGEMENT =====
    // CRUD operations untuk dashboard slider images
    Route::resource('sliders', SliderController::class);
    
    // Custom slider action untuk quick toggle enable/disable
    Route::post('/sliders/{slider}/toggle-active', [SliderController::class, 'toggleActive'])->name('sliders.toggleActive');

    // ===== REPORTS & ANALYTICS =====
    // Admin reports untuk monitoring dan analysis
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('/technicians', [ReportController::class, 'technicians'])->name('technicians');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
    });
});

// ============================================
// 4. TECHNICIAN ROUTES (requires auth + technician role)
// ============================================

/**
 * Technician Routes
 * 
 * Middleware: auth + technician
 * Hanya accessible oleh users dengan role = 'technician'
 * Prefix: /technician (untuk separation)
 * 
 * Termasuk:
 * - Technician dashboard dengan statistics
 * - View assigned bookings (pesanan masuk)
 * - Manage tugas/tasks
 * - View earnings/penghasilan
 * - View ratings & reviews
 * - Edit profil technician
 */
Route::middleware(['auth', 'technician'])->prefix('technician')->name('technician.')->group(function () {
    // ===== DASHBOARD =====
    Route::get('/dashboard', [TechnicianDashboardController::class, 'index'])->name('dashboard');
    
    // ===== BOOKINGS MANAGEMENT =====
    // Pesanan yang ditugaskan ke technician ini
    Route::get('/bookings', [TechnicianBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [TechnicianBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/mark-completed', [TechnicianBookingController::class, 'markAsCompleted'])->name('bookings.markAsCompleted');
    Route::post('/bookings/{booking}/accept', [TechnicianBookingController::class, 'accept'])->name('bookings.accept');
    Route::post('/bookings/{booking}/reject', [TechnicianBookingController::class, 'reject'])->name('bookings.reject');
    
    // ===== TASKS MANAGEMENT =====
    // Tugas/task yang harus dikerjakan
    Route::get('/tasks', [TechnicianTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{booking}', [TechnicianTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{booking}/start', [TechnicianTaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{booking}/complete', [TechnicianTaskController::class, 'complete'])->name('tasks.complete');
    Route::put('/tasks/{booking}', [TechnicianTaskController::class, 'update'])->name('tasks.update');
    
    // ===== PROFILE MANAGEMENT =====
    Route::get('/profile', [TechnicianProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [TechnicianProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [TechnicianProfileController::class, 'update'])->name('profile.update');
    
    // ===== EARNINGS/INCOME =====
    Route::get('/earnings', [TechnicianEarningsController::class, 'index'])->name('earnings.index');
    Route::get('/earnings/details', [TechnicianEarningsController::class, 'details'])->name('earnings.details');
    Route::get('/earnings/export', [TechnicianEarningsController::class, 'export'])->name('earnings.export');
    
    // ===== RATINGS & REVIEWS =====
    Route::get('/ratings', [TechnicianRatingController::class, 'index'])->name('ratings.index');
    Route::get('/ratings/{booking}', [TechnicianRatingController::class, 'show'])->name('ratings.show');
});

// ============================================
// 5. CUSTOMER ROUTES (requires auth + customer role)
// ============================================

/**
 * Customer Routes
 * 
 * Middleware: auth + customer
 * Hanya accessible oleh users dengan role = 'customer'
 * Prefix: /customer (untuk separation dari admin)
 * 
    // ===== PAYMENT SUBMISSION =====
    // Customer-facing payment submission workflow
    Route::get('/invoices/{invoice}/payment-form', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/invoices/{invoice}/payment-submit', [PaymentController::class, 'submitPayment'])->name('payment.submit');
    Route::get('/invoices/{invoice}/payment-history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    Route::delete('/payments/{payment}/cancel', [PaymentController::class, 'cancelPayment'])->name('payment.cancel');
 * Termasuk:
 * - Customer dashboard dengan booking history
 * - Customer-specific operations (booking creation, tracking, etc)
 */
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // Services - List all available services
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    
    // Bookings - Customer CRUD for own bookings
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [CustomerBookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [CustomerBookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [CustomerBookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    
    // Invoices - Customer view own invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');

    // Payments - Customer payment submission
    Route::get('/invoices/{invoice}/payment-form', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/invoices/{invoice}/payment-submit', [PaymentController::class, 'submitPayment'])->name('payment.submit');
    Route::get('/invoices/{invoice}/payment-history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    
    // Payments - Enhanced FASE 2 (Shopee-like checkout)
    Route::get('/invoices/{invoice}/checkout', [PaymentController::class, 'showCheckout'])->name('payment.checkout');
    Route::get('/payments/{payment}/progress', [PaymentController::class, 'showProgress'])->name('payment.progress');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'showReceipt'])->name('payment.receipt');
    Route::get('/payments/{payment}/download-receipt', [PaymentController::class, 'downloadReceipt'])->name('payment.download-receipt');
    Route::delete('/payments/{payment}/cancel', [PaymentController::class, 'cancelPayment'])->name('payment.cancel');
    Route::get('/api/payments/{payment}/progress', [PaymentController::class, 'getPaymentProgress'])->name('payment.api-progress');
    
    // Ratings - Customer rate services and technicians
    Route::get('/bookings/{booking}/ratings', [RatingController::class, 'showForBooking'])->name('ratings.show');
    Route::post('/bookings/{booking}/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::put('/ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
});

// ============================================
// 6. WEBHOOK ROUTES (External Payment Gateway)
// ============================================

/**
 * Payment Gateway Webhooks
 * 
 * Public routes untuk menerima callbacks dari payment gateway (Midtrans)
 * NOT protected by authentication (Midtrans akan POST ke endpoint ini)
 * Signature verification dilakukan di controller
 * 
 * Endpoint:
 * - POST /webhooks/midtrans - Midtrans payment notification
 */
Route::post('/webhooks/midtrans', [PaymentWebhookController::class, 'midtransNotification'])->name('webhook.midtrans');

// Payment status check API (untuk customer check payment progress)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/payments/{payment}/status', [PaymentWebhookController::class, 'getPaymentStatus'])->name('payment.status');
}); 