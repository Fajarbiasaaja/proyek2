# 📚 JasaKu - Complete System Documentation Index

## 📋 Quick Reference

Dokumentasi lengkap aplikasi JasaKu telah disusun dalam beberapa file untuk kemudahan:

### 📖 File Dokumentasi
1. **[FEATURES_BY_ROLE.md](FEATURES_BY_ROLE.md)** - Daftar lengkap fitur per role
2. **[WORKFLOW_AND_STATUS.md](WORKFLOW_AND_STATUS.md)** - Status transitions & workflow
3. **[IMPLEMENTATION_GUIDE.md](IMPLEMENTATION_GUIDE.md)** - Guide implementasi & checklist
4. **[TESTING_SCENARIOS.md](TESTING_SCENARIOS.md)** - Test cases & edge cases

---

## 🎯 Role-Based System Overview

### Tiga Role Utama:

#### 👥 **CUSTOMER** (Pelanggan)
- Register/Login (email/OAuth)
- Browse services tersedia
- Create & manage bookings
- Submit payments untuk invoices
- Give ratings & reviews
- View booking history

**Key Routes:**
```
GET  /customer/dashboard           - Dashboard
GET  /customer/services            - Browse services
GET  /customer/bookings            - Booking history
POST /customer/bookings            - Create booking
GET  /customer/invoices            - View invoices
POST /customer/invoices/{id}/payment-submit - Submit payment
POST /customer/bookings/{id}/ratings - Give rating
```

---

#### 🔧 **TECHNICIAN** (Penyedia Jasa/Teknisi)
- Register sebagai service provider
- Manage own profile
- View assigned bookings
- Complete work & add notes
- View ratings dari customers
- Track performance stats

**Key Routes:**
```
GET  /technician/dashboard         - Dashboard & stats
GET  /technician/bookings          - Assigned bookings
POST /technician/bookings/{id}/mark-completed - Mark done
GET  /technicians/{id}/ratings     - View own ratings
```

---

#### 👨‍💼 **ADMIN** (Administrator)
- Dashboard dengan statistics
- Manage customers (CRUD)
- Manage technicians (CRUD)
- Manage services (CRUD)
- Manage bookings & assign technicians
- Approve/reject payments
- View comprehensive reports

**Key Routes:**
```
GET  /dashboard                    - Admin dashboard
GET  /customers                    - List customers
GET  /technicians                  - List technicians
GET  /services                     - List services
GET  /bookings                     - List all bookings
GET  /payments/pending             - Pending payments
GET  /reports/*                    - Various reports
GET  /sliders                      - Manage homepage
```

---

## 🔄 Core Workflows

### Workflow 1: Customer Booking → Payment → Rating

```
1. CUSTOMER CREATE BOOKING
   └─ POST /customer/bookings
      ├─ Select service
      ├─ Choose date/time
      ├─ Add notes
      └─ Create (status: pending)

2. ADMIN CONFIRMS & ASSIGNS
   └─ PUT /bookings/{id}
      ├─ Assign technician
      └─ Status → confirmed

3. TECHNICIAN COMPLETES WORK
   └─ POST /technician/bookings/{id}/mark-completed
      ├─ Add completion notes
      └─ Status → completed

4. CUSTOMER SUBMITS PAYMENT
   └─ POST /customer/invoices/{id}/payment-submit
      ├─ Choose payment method
      ├─ Submit proof (if bank transfer)
      └─ Status → pending approval

5. ADMIN APPROVES PAYMENT
   └─ POST /payments/{id}/approve
      └─ Status → approved → confirmed

6. CUSTOMER GIVES RATING
   └─ POST /customer/bookings/{id}/ratings
      ├─ Select stars (1-5)
      ├─ Add review
      └─ Rating saved
```

---

### Workflow 2: Status Transitions

#### Booking Status Flow:
```
pending → confirmed → in_progress → completed → [RATING AVAILABLE]
         ↓                ↓              ↓
         └────── cancelled ◄─────────────┘
```

#### Payment Status Flow:
```
pending → approved → confirmed (paid)
   ↓
[rejected] → pending (resubmit)
```

---

## 📊 Database Entity Relationships

```
User (1) ──┬─→ (Many) Booking (customer_id)
           ├─→ (Many) Technician (user_id)
           └─→ (Many) Rating (customer_id)

Booking (1) ──┬─→ Customer (customer_id)
              ├─→ Technician (technician_id, nullable)
              ├─→ Service (service_id)
              ├─→ Invoice (1-to-1)
              └─→ Rating (1-to-1)

Invoice (1) ──→ (Many) Payment

Rating (Many) → Technician (for averaging)
```

---

## 🔐 Security & Authorization

### Middleware Layer:
```php
middleware('auth')              // Authenticated users only
middleware('admin')             // Admin role only
middleware('customer')          // Customer role only
middleware('technician')        // Technician role only
```

### Data Visibility Rules:
| Entity | Customer | Technician | Admin |
|--------|----------|-----------|-------|
| Own bookings | ✅ | ✅ | - |
| All bookings | - | - | ✅ |
| Own invoices | ✅ | - | ✅ |
| Own ratings | ✅ | ✅ | ✅ |
| User data | Own | Own | All |

---

## 🧪 Testing Priority

### Critical Paths (Test First):
1. ✅ Customer registration & login
2. ✅ Complete booking workflow
3. ✅ Payment submission & approval
4. ✅ Rating system
5. ✅ Role-based access control

### Important Flows (Test Second):
6. ✅ Booking status transitions
7. ✅ Technician assignment
8. ✅ Invoice generation
9. ✅ Concurrent bookings handling

### Nice-to-Have (Test Last):
10. ✅ Report generation
11. ✅ Admin bulk operations
12. ✅ Performance under load

---

## 🚀 Quick Start for Developers

### Step 1: Understand the System
- Read: **FEATURES_BY_ROLE.md** (5 min)
- Read: **WORKFLOW_AND_STATUS.md** (10 min)

### Step 2: Setup Development Environment
```bash
composer install
php artisan migrate
php artisan db:seed

# Create test users
- Admin: admin@example.com / password
- Customer: customer@example.com / password
- Technician: tech@example.com / password
```

### Step 3: Implement/Test Features
- Check: **IMPLEMENTATION_GUIDE.md** for detailed implementation
- Check: **TESTING_SCENARIOS.md** for test cases

### Step 4: Test & Deploy
```bash
php artisan test
npm run build (for frontend assets)
php artisan migrate --env=production
```

---

## 📱 API Endpoints Summary

### Public Endpoints (No Auth Required)
```
GET  /                                    - Landing page
GET  /login                               - Login form
POST /login                               - Process login
POST /register                            - Process registration
GET  /login/{provider}                    - OAuth redirect
GET  /login/{provider}/callback           - OAuth callback
GET  /technicians/{id}/ratings            - Public ratings
```

### Customer Endpoints (Auth + Customer Role)
```
GET    /customer/dashboard
GET    /customer/services
GET    /customer/bookings
POST   /customer/bookings
PUT    /customer/bookings/{id}
DELETE /customer/bookings/{id}
POST   /customer/bookings/{id}/cancel
GET    /customer/invoices
POST   /customer/invoices/{id}/payment-submit
POST   /customer/bookings/{id}/ratings
PUT    /ratings/{id}
DELETE /ratings/{id}
```

### Technician Endpoints (Auth + Technician Role)
```
GET  /technician/dashboard
GET  /technician/bookings
GET  /technician/bookings/{id}
POST /technician/bookings/{id}/mark-completed
```

### Admin Endpoints (Auth + Admin Role)
```
GET    /dashboard
GET    /customers, POST, PUT, DELETE
GET    /technicians, POST, PUT, DELETE
GET    /services, POST, PUT, DELETE
GET    /bookings, POST, PUT, DELETE
POST   /bookings/{id}/cancel
POST   /bookings/{id}/mark-completed
GET    /payments/pending
POST   /payments/{id}/approve
POST   /payments/{id}/reject
GET    /reports/*
GET    /sliders, POST, PUT, DELETE
```

### Webhook Endpoints (External)
```
POST   /webhooks/midtrans          - Midtrans payment callback
GET    /api/payments/{id}/status   - Payment status check
```

---

## 🎨 Frontend Views Structure (Blade/Vue)

### Public Pages:
```
resources/views/
├── welcome.blade.php              - Landing page
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── register-provider.blade.php
```

### Customer Dashboard:
```
resources/views/customer/
├── dashboard.blade.php
├── bookings/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── edit.blade.php
├── invoices/
│   ├── index.blade.php
│   └── show.blade.php
└── ratings/
    └── form.blade.php
```

### Admin Dashboard:
```
resources/views/admin/
├── dashboard.blade.php
├── customers/ (CRUD)
├── technicians/ (CRUD)
├── services/ (CRUD)
├── bookings/ (CRUD + actions)
├── payments/ (pending list, approve/reject)
├── reports/
└── sliders/ (CRUD)
```

### Technician Dashboard:
```
resources/views/technician/
├── dashboard.blade.php
├── bookings/
│   ├── index.blade.php
│   └── show.blade.php
└── ratings.blade.php
```

---

## 🔧 Configuration Files

Key configuration files to review:

```
config/
├── app.php                 - App name, timezone, etc
├── auth.php               - Auth guards & providers
├── database.php           - Database connection
├── mail.php               - Email configuration
├── services.php           - OAuth providers (Google, etc)
└── session.php            - Session configuration

.env                        - Environment variables
```

**Important .env Variables:**
```
APP_NAME=JasaKu
APP_ENV=production
APP_DEBUG=false
DATABASE_URL=mysql://...
MAIL_FROM_ADDRESS=noreply@jasaku.com
MIDTRANS_SERVER_KEY=...
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

---

## 📈 Analytics & Monitoring

### Key Metrics to Track:
1. **Business Metrics:**
   - Total bookings created
   - Completion rate
   - Total revenue
   - Average booking value
   - Customer satisfaction (avg rating)

2. **Technician Metrics:**
   - Bookings completed per technician
   - Average rating per technician
   - Earnings per technician
   - Response time to bookings

3. **System Metrics:**
   - Page load times
   - API response times
   - Database query times
   - Error rates
   - User retention

### Where to Find Reports:
```
Admin Dashboard:
GET /dashboard                  - Quick stats
GET /reports/revenue           - Revenue analytics
GET /reports/bookings          - Booking statistics
GET /reports/technicians       - Technician performance
GET /reports/customers         - Customer insights
GET /reports/payments          - Payment tracking
```

---

## 🆘 Common Issues & Solutions

### Issue 1: "Unauthorized" on Booking Page
**Solution:** Check user role
```php
// Verify middleware is applied
Route::middleware(['auth', 'customer'])->group(...);
```

### Issue 2: Payment Status Stuck on Pending
**Solution:** Check admin approval or webhook
```php
// Verify Midtrans webhook is configured
POST /webhooks/midtrans
```

### Issue 3: Technician Not Assigned
**Solution:** Admin must assign before confirming
```
Booking stays pending until admin assigns technician
Then status changes to confirmed
```

### Issue 4: Double Charging Customer
**Solution:** One invoice per booking only
```php
// Check: One booking = One invoice
$booking->invoice()->create(...);
```

---

## 📞 Support & Documentation

For specific questions, refer to:

| Question | File |
|----------|------|
| "What features exist for customer?" | FEATURES_BY_ROLE.md → CUSTOMER section |
| "What happens after booking completed?" | WORKFLOW_AND_STATUS.md → BOOKING section |
| "How to implement new controller?" | IMPLEMENTATION_GUIDE.md → Implementation Details |
| "Test case for rating system?" | TESTING_SCENARIOS.md → 1.4 Rating & Review |
| "API endpoint for payments?" | FEATURES_BY_ROLE.md → 1.5 Invoice & Pembayaran |

---

## 📅 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Apr 26, 2026 | Initial complete documentation |

---

## ✨ Next Steps

1. **Review Documentation** (30 min)
   - Read all 4 documentation files
   - Understand system architecture

2. **Setup Development** (30 min)
   - Clone repo
   - Run migrations
   - Create test users

3. **Test Core Workflows** (2 hours)
   - Test customer booking flow
   - Test payment approval
   - Test technician assignment

4. **Implement Features** (As needed)
   - Follow IMPLEMENTATION_GUIDE.md
   - Reference TESTING_SCENARIOS.md for validation

5. **Deploy to Production** (As ready)
   - Follow deployment steps in IMPLEMENTATION_GUIDE.md
   - Set up monitoring

---

**Created:** April 26, 2026  
**System:** JasaKu (Service Booking Platform)  
**Status:** ✅ Complete Documentation  

**Questions?** Refer to the 4 main documentation files above.
