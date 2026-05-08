# 🚀 Implementation Guide & Role Setup Checklist

## ✅ Pre-Launch Checklist

### Database & Models
- [x] User model dengan role column (admin/customer/technician)
- [x] Customer model (relasi ke User)
- [x] Technician model (relasi ke User)
- [x] Service model (layanan yang tersedia)
- [x] Booking model (pemesanan)
- [x] Invoice model (tagihan)
- [x] Payment model (pembayaran)
- [x] Rating model (review/rating)
- [x] Slider model (homepage carousel)

### Controllers Setup
**Authentication**
- [x] AuthController (login, register, logout)
- [x] SocialAuthController (OAuth: Google, Facebook, GitHub)
- [x] ProfileController (edit email, password, profile)

**Customer Features**
- [x] CustomerDashboardController
- [x] CustomerBookingController
- [x] ServiceController
- [x] InvoiceController
- [x] PaymentController
- [x] RatingController

**Technician Features**
- [x] TechnicianDashboardController
- [x] TechnicianController (CRUD untuk admin)

**Admin Features**
- [x] DashboardController
- [x] CustomerController
- [x] BookingController
- [x] SliderController
- [x] ReportController
- [x] PaymentWebhookController (Midtrans integration)

### Middleware Setup
- [x] CheckAdminRole (`middleware.admin`)
- [x] CheckCustomerRole (`middleware.customer`)
- [x] CheckTechnicianRole (`middleware.technician`)

### Routes Organization
- [x] Public routes (/, login, register, logout, OAuth)
- [x] Auth routes (/profile/*)
- [x] Admin routes (/dashboard, /customers, /technicians, /services, /bookings, /invoices, /payments, /sliders, /reports)
- [x] Customer routes (/customer/dashboard, /customer/bookings, /customer/invoices, /customer/services)
- [x] Technician routes (/technician/dashboard, /technician/bookings)
- [x] Webhook routes (/webhooks/midtrans)

---

## 🔧 Implementation Details by Feature

### 1. CUSTOMER REGISTRATION & LOGIN

**Files Involved:**
- `app/Http/Controllers/AuthController.php`
- `app/Models/User.php`
- `routes/web.php` (POST /register, POST /login)

**Implementation Points:**
```php
// Registration Flow
1. User fills form (name, email, password)
2. Validate input (unique email, password strength)
3. Hash password before storing
4. Set role = 'customer' by default
5. Send verification email (optional)
6. Redirect to login or auto-login

// Login Flow
1. User enters email & password
2. Validate credentials
3. Create session
4. Check role (admin/customer/technician)
5. Redirect to appropriate dashboard
```

**Key Considerations:**
- ✅ Password must be hashed (Laravel HashServiceProvider)
- ✅ Email must be unique
- ✅ OAuth integration for social login (Google, Facebook, GitHub)
- ✅ Auto-redirect based on role

---

### 2. CUSTOMER BOOKING WORKFLOW

**Files Involved:**
- `app/Http/Controllers/CustomerBookingController.php`
- `app/Models/Booking.php`
- `app/Models/Invoice.php`
- `routes/web.php` (/customer/bookings/*)

**Status Flow:**
```
pending → confirmed → in_progress → completed
   ↓                       ↑
   └─────── cancelled ◄────┘
```

**Implementation Steps:**

#### Step 1: Customer Create Booking (GET /customer/bookings/create)
```php
1. Display form dengan:
   - Service dropdown (daftar layanan)
   - Schedule date/time picker
   - Location/address field
   - Notes textarea
   - Price preview (calculated from service + date)

2. Show technician recommendations based on:
   - Service expertise match
   - Availability
   - Top ratings
```

#### Step 2: Submit Booking (POST /customer/bookings)
```php
1. Validate input:
   - Service exists & is active
   - Scheduled date is in future
   - Customer doesn't have conflicting pending bookings
   
2. Create booking dengan status='pending'
   - customer_id = Auth::id()
   - service_id = $request->service_id
   - scheduled_date = $request->date
   - status = 'pending'
   - technician_id = null (will be assigned by admin)
   
3. Auto-create invoice:
   - invoice_id generated
   - items = booking service
   - subtotal = service.price
   - tax = calculated (10% or config)
   - total = subtotal + tax
   - status = 'unpaid'
   
4. Send notification to customer:
   - "Booking submitted successfully"
   - "Awaiting admin confirmation"
   
5. Notify admin:
   - "New booking pending: [customer] - [service]"
   - Link to review & assign technician
```

#### Step 3: Admin Confirm & Assign (PUT /bookings/{id})
```php
1. Admin view booking detail at /bookings/{id}
2. Select available technician
   - Filter by service specialization
   - Check availability
   - Show rating
3. Set booking status = 'confirmed'
4. Auto-notification to customer:
   - "Booking confirmed"
   - "Technician assigned: [name]"
   - "Scheduled for: [date/time]"
5. Notification to technician:
   - "New booking assigned"
   - "Customer: [name]"
   - "Service: [name]"
   - "Schedule: [date/time]"
```

#### Step 4: Technician Start Work
```php
1. Technician view /technician/bookings
2. Technician see assigned booking
3. Technician ready to start (auto or manual update status)
4. Status changed to 'in_progress'
5. Notification to customer:
   - "Work started"
   - "Technician on the way / arrived"
```

#### Step 5: Technician Complete Work
```php
1. Technician click "Mark as Completed" on /technician/bookings/{id}
2. Form appears untuk add completion notes
   - What was done
   - Issues encountered
   - Recommendations
   - Signature/before-after photos (optional)
3. Status changed to 'completed'
4. Notifications:
   - Customer: "Work completed, ready for payment & rating"
   - Admin: "Booking completed, awaiting payment"
```

#### Step 6: Customer Payment & Rating
```
After booking completed:
1. Customer can submit payment (see invoice)
2. Customer can give rating (1-5 stars + review)
3. Only after completed status

Booking Visible in Dashboard:
- Pending: Awaiting admin confirmation
- Confirmed: Scheduled for [date]
- In Progress: Technician working
- Completed: Done, awaiting payment
```

---

### 3. PAYMENT WORKFLOW

**Files Involved:**
- `app/Http/Controllers/PaymentController.php`
- `app/Http/Controllers/PaymentWebhookController.php`
- `app/Models/Payment.php`
- `app/Models/Invoice.php`

**Status Flow:**
```
pending → approved → confirmed (paid)
```

**Implementation Points:**

#### Payment Submission (Customer Side)
```php
Route: GET /customer/invoices/{invoice}/payment-form

1. Customer view invoice detail
2. Click "Pay Now"
3. Form shows:
   - Invoice total
   - Available payment methods (bank transfer, e-wallet, etc)
   - Payment proof upload (if bank transfer)
   - Notes field

4. Submit payment:
   - Create Payment record (status='pending')
   - Send to Midtrans payment gateway (if available)
   - Or save proof for admin approval
   
Route: POST /customer/invoices/{invoice}/payment-submit
   - Store payment proof
   - Set status='pending'
   - Notify admin to approve
```

#### Payment Approval (Admin Side)
```php
Route: GET /payments/pending
- List all pending payments waiting admin approval
- Show payment proof/evidence

Route: POST /payments/{payment}/approve
- Admin review payment proof
- Click "Approve"
- Payment status = 'approved'
- Notify customer: "Payment approved, processing..."

Route: POST /payments/{payment}/reject
- Admin find issue with proof
- Click "Reject"
- Payment status still 'pending' (customer resubmit)
- Notify customer: "Payment rejected, please resubmit"
- Show rejection reason
```

#### Webhook Integration (Midtrans)
```php
Route: POST /webhooks/midtrans

When payment gateway confirmed:
1. Midtrans send POST to this endpoint
2. Verify webhook signature (security)
3. Update payment status = 'confirmed'
4. Mark invoice as paid
5. Notify customer: "Payment successful"
6. Update booking invoice status
```

#### Payment Cancellation
```php
Route: DELETE /payments/{payment}/cancel

Before payment confirmed:
1. Customer can cancel pending payment
2. Payment status = 'cancelled'
3. Payment record soft-deleted (audit trail)
4. Invoice still unpaid
5. Customer can submit new payment
```

---

### 4. RATING & REVIEW SYSTEM

**Files Involved:**
- `app/Http/Controllers/RatingController.php`
- `app/Models/Rating.php`

**Flow:**
```
Booking Completed
    ↓
Customer Access Rating Form
GET /customer/bookings/{booking}/ratings
    ↓
Customer Fill & Submit
POST /customer/bookings/{booking}/ratings
    ↓
Rating Saved & Visible
- Technician profile
- Admin dashboard
- Public ratings list
```

**Implementation:**

#### View Rating Form
```php
Route: GET /customer/bookings/{booking}/ratings

Requirements:
- Booking status MUST be 'completed'
- Only booking customer can access
- Show form with:
  * Rating (1-5 stars radio/select)
  * Review (textarea, max 1000 chars)
  * Submit button
```

#### Store Rating
```php
Route: POST /customer/bookings/{booking}/ratings

1. Validate:
   - User is booking customer (auth check)
   - Booking status = 'completed'
   - Rating between 1-5
   - Review max 1000 chars
   - No duplicate rating for this booking

2. Create Rating:
   - booking_id
   - customer_id
   - technician_id (from booking.technician_id)
   - rating (1-5)
   - review (nullable)
   - created_at

3. Update Technician stats:
   - Recalculate average_rating
   - Increment total_ratings count
   - Update in technician profile

4. Response:
   - Success message
   - Redirect to booking detail
```

#### Update Rating
```php
Route: PUT /ratings/{rating}

Only owner (customer who created rating) can update
- Change rating value
- Update review text
- Save changes
```

#### Delete Rating
```php
Route: DELETE /ratings/{rating}

Only owner can delete
- Remove rating from technician average calculation
- Soft delete or hard delete (configurable)
```

#### View Public Ratings
```php
Route: GET /technicians/{technician}/ratings

Public endpoint (no auth required)
Shows:
- All ratings for technician
- Average rating
- Rating distribution (stars 1-5)
- Individual reviews
```

---

### 5. ADMIN DASHBOARD & REPORTS

**Files Involved:**
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/ReportController.php`

**Dashboard Displays:**
```
Statistics Cards:
├─ Total Bookings (all-time)
├─ Total Revenue (paid invoices)
├─ Pending Payments
├─ Active Bookings (in_progress count)
├─ Total Customers
├─ Total Technicians
└─ Avg Technician Rating

Recent Activity:
├─ Latest bookings
├─ Recent payments
├─ New customers
└─ System updates

Quick Actions:
├─ Create Booking
├─ View Pending Payments
├─ Manage Sliders
└─ Generate Report
```

**Reports Available:**
```
1. Revenue Report
   - By period (day/week/month/year)
   - By service
   - By technician
   - Growth trend

2. Booking Report
   - By status (pending/confirmed/completed/cancelled)
   - By service type
   - By technician
   - Completion rate
   - Avg booking value

3. Technician Performance
   - Total bookings completed
   - Avg rating
   - Total earnings
   - Customer satisfaction

4. Customer Report
   - New customers (by period)
   - Repeat customers
   - Avg spending per customer
   - Customer satisfaction

5. Payment Report
   - Total revenue
   - Payment methods breakdown
   - Pending vs paid
   - Payment delay analysis
```

---

## 🛡️ Security Implementation

### Role-Based Access Control (Middleware)
```php
// In app/Http/Middleware/CheckAdminRole.php
public function handle($request, Closure $next)
{
    if (Auth::check() && Auth::user()->role === 'admin') {
        return $next($request);
    }
    
    abort(403, 'Unauthorized');
}

// Same for CheckCustomerRole, CheckTechnicianRole
```

### Route Protection
```php
// In routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes only
});

Route::middleware(['auth', 'customer'])->group(function () {
    // Customer routes only
});

Route::middleware(['auth', 'technician'])->group(function () {
    // Technician routes only
});
```

### Data Authorization (Controller Level)
```php
// Example: Customer can only view own bookings
public function show(Booking $booking)
{
    // Check authorization
    if ($booking->customer_id !== Auth::id()) {
        abort(403, 'Unauthorized');
    }
    
    return view('booking.show', ['booking' => $booking]);
}
```

### Sensitive Data Protection
```php
// In User model
protected $hidden = [
    'password',
    'remember_token',
];

// Audit trail with soft deletes
use SoftDeletes;
// When delete, data is preserved in deleted_at column
```

---

## 📊 Key Implementation Patterns

### 1. Model Relationships
```php
// User → Booking (customer)
public function bookings() {
    return $this->hasMany(Booking::class, 'customer_id');
}

// Booking → Invoice
public function invoice() {
    return $this->hasOne(Invoice::class);
}

// Invoice → Payment
public function payments() {
    return $this->hasMany(Payment::class);
}

// Booking → Rating
public function rating() {
    return $this->hasOne(Rating::class);
}
```

### 2. Query Optimization
```php
// Eager load related data
$bookings = Booking::with(['customer', 'service', 'technician'])
    ->where('customer_id', Auth::id())
    ->paginate(15);

// Use indexes on frequently queried columns
- bookings.customer_id
- bookings.status
- payments.status
- ratings.technician_id
```

### 3. Status Management
```php
// Use enum constants
const STATUS_PENDING = 'pending';
const STATUS_CONFIRMED = 'confirmed';
const STATUS_IN_PROGRESS = 'in_progress';
const STATUS_COMPLETED = 'completed';
const STATUS_CANCELLED = 'cancelled';

// Validation
$booking->validate([
    'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled'
]);
```

---

## 🧪 Testing Checklist

### Unit Tests
- [ ] User authentication (login, register, logout)
- [ ] Role-based access (admin, customer, technician)
- [ ] Booking creation & status transitions
- [ ] Payment processing
- [ ] Rating creation & validation

### Integration Tests
- [ ] Complete booking workflow (create → confirm → complete → rate)
- [ ] Payment flow (submit → approve → confirm)
- [ ] User dashboard access
- [ ] Report generation

### Manual Testing
- [ ] Test each role login/logout
- [ ] Create booking as customer
- [ ] Assign technician as admin
- [ ] Complete booking as technician
- [ ] Submit payment as customer
- [ ] Approve payment as admin
- [ ] Rate booking as customer
- [ ] View reports as admin

---

## 🚀 Deployment Steps

1. **Environment Setup**
   ```
   cp .env.example .env
   php artisan key:generate
   ```

2. **Database**
   ```
   php artisan migrate
   php artisan db:seed (if seeders exist)
   ```

3. **Create Default Users**
   ```
   - Admin account (for testing/management)
   - Demo customer account
   - Demo technician account
   ```

4. **Configuration**
   ```
   - Set up mail (for notifications)
   - Configure payment gateway (Midtrans)
   - Set up OAuth providers
   - Configure storage for uploads
   ```

5. **Verify Permissions**
   ```
   - Check file/folder permissions
   - Verify storage directory is writable
   - Check cache directory is writable
   ```

---

**Last Updated:** April 26, 2026
**Version:** 1.0 - Complete Implementation Guide
