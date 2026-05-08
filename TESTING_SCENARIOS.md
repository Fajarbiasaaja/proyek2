# 🧪 Testing Scenarios & Edge Cases

## 1. CUSTOMER ROLE TESTING

### 1.1 Registration & Authentication
#### Scenario: Valid Registration
```
Given: User on /register page
When: User fills form with:
  - name: John Doe
  - email: john@example.com (unique)
  - password: SecurePass123! (min 8 chars, uppercase, lowercase, number)
And: User clicks "Register"
Then: 
  - Account created with role='customer'
  - Redirect to login page
  - Success message displayed
  - Email verification sent (optional)
```

#### Scenario: Duplicate Email Registration
```
Given: Email john@example.com already exists
When: New user tries to register with same email
Then:
  - Validation error: "Email already in use"
  - Form repopulated (keep name, clear password)
  - User stays on register page
```

#### Scenario: Invalid Password
```
Given: User enters password: "123"
When: Password < 8 characters
Then:
  - Validation error: "Password must be at least 8 characters"
  - Form still displayed
  - User can correct and resubmit
```

#### Scenario: Successful Login
```
Given: User credentials are valid (john@example.com / SecurePass123!)
When: User fills login form and clicks "Login"
Then:
  - Session created
  - Redirect to /customer/dashboard
  - Navigation shows customer name
  - Logout option visible
```

#### Scenario: Failed Login
```
Given: User enters wrong password
When: User submits login form
Then:
  - Error message: "Invalid credentials"
  - Form cleared (security)
  - Stays on login page
  - Login attempt logged (security)
```

#### Scenario: OAuth Login (Google)
```
Given: User on login page
When: User clicks "Login with Google"
Then:
  - Redirect to Google OAuth dialog
  - User authorizes app
  - Callback to /login/google/callback
  - If first time: New customer account created
  - If exists: Account linked (if email matches)
  - Redirect to /customer/dashboard
```

---

### 1.2 Booking Management
#### Scenario: Create Valid Booking
```
Given: Customer on /customer/bookings/create
When: Customer fills form:
  - Service: "AC Cleaning" (id=1)
  - Date: Tomorrow 10:00 AM (future date)
  - Location: "Jl. Example No. 123"
  - Notes: "Please check thermostat too"
And: Customer submits form
Then:
  - Booking created (status='pending')
  - customer_id = logged in user
  - technician_id = NULL
  - Invoice auto-generated (status='unpaid')
  - Redirect to /customer/bookings/{id}
  - Success message: "Booking created successfully"
  - Admin notified
```

#### Scenario: Schedule Past Date
```
Given: Customer tries to book for yesterday
When: Past date in form
Then:
  - Validation error: "Date must be in the future"
  - Form stays open
```

#### Scenario: Invalid Service
```
Given: Customer selects non-existent service
When: Booking form submitted with service_id=999
Then:
  - Validation error: "Selected service not found"
  - Booking not created
```

#### Scenario: Concurrent Booking Check (Future Feature)
```
Given: Customer already has pending booking for same date/time
When: Customer tries to create another
Then:
  - Warning: "You already have booking for this time"
  - Option to cancel old one or choose different time
```

#### Scenario: Edit Pending Booking
```
Given: Booking status = 'pending'
When: Customer clicks "Edit" at /customer/bookings/{id}/edit
Then:
  - Form pre-filled with current data
  - Can edit all fields (service, date, location, notes)
  - Submit saves changes
  - Invoice not affected (same total)
```

#### Scenario: Cannot Edit Confirmed Booking
```
Given: Booking status = 'confirmed'
When: Customer tries to access edit page
Then:
  - Edit button disabled/hidden
  - Message: "Cannot edit confirmed booking"
  - Only cancel option available
```

#### Scenario: Cancel Booking
```
Given: Booking in ANY status (pending/confirmed/in_progress/completed)
When: Customer clicks "Cancel" and confirms
Then:
  - Status changed to 'cancelled'
  - Invoice status = 'cancelled'
  - Payment cancelled if any
  - Notification sent to:
    * Admin
    * Technician (if assigned)
  - Customer receives confirmation
```

#### Scenario: View Booking History
```
Given: Customer on /customer/bookings
Then: Display all bookings paginated (15 per page):
  - Pending bookings (at top, ordered by schedule date)
  - Confirmed bookings
  - In progress bookings
  - Completed bookings
  - Cancelled bookings

Each shows:
  - Service name
  - Scheduled date/time
  - Technician name (if assigned)
  - Current status
  - Action buttons (view, edit, cancel, rate)
```

---

### 1.3 Payment Processing
#### Scenario: View Invoice
```
Given: Booking completed, invoice created
When: Customer goes to /customer/invoices
Then: List shows:
  - Invoice number (auto-generated)
  - Service booked
  - Amount (subtotal + tax)
  - Status (unpaid/paid)
  - Due date
```

#### Scenario: Submit Payment - Bank Transfer
```
Given: Customer on invoice detail page
When: Customer clicks "Pay Now"
Then: Form shows:
  - Invoice total: Rp 500.000
  - Payment methods (bank transfer, e-wallet)
  - Customer selects "Bank Transfer"
  - Paste bank details to copy
Then: 
  - Customer selects "Bank Transfer"
  - Upload payment proof (screenshot)
  - Add notes "Transferred from BCA"
  - Submit
Then:
  - Payment created (status='pending')
  - Admin notified to approve
  - Customer sees: "Payment submitted, waiting approval"
```

#### Scenario: Submit Payment - E-Wallet (Midtrans)
```
Given: Payment gateway configured
When: Customer selects e-wallet payment method
Then:
  - Redirect to Midtrans payment page
  - Customer chooses e-wallet (OVO, Dana, etc)
  - Complete payment in e-wallet app
  - Webhook callback to /webhooks/midtrans
  - Payment auto-confirmed
  - Invoice marked paid
  - Customer notified
```

#### Scenario: Invalid Payment Proof
```
Given: Customer uploads unclear/invalid proof
When: Admin reviews and finds issue
And: Admin clicks "Reject"
Then:
  - Payment status stays 'pending'
  - Rejection reason sent to customer
  - Customer can resubmit
```

#### Scenario: Cancel Pending Payment
```
Given: Payment status = 'pending'
When: Customer clicks "Cancel Payment"
Then:
  - Payment cancelled
  - Invoice still unpaid
  - Customer can submit new payment
```

#### Scenario: View Payment History
```
Given: Customer on /customer/invoices/{invoice}/payment-history
Then: Show all payment attempts:
  - Date submitted
  - Amount
  - Method (bank/e-wallet)
  - Status (pending/approved/confirmed/rejected)
  - Rejection reason (if any)
```

---

### 1.4 Rating & Review
#### Scenario: Cannot Rate Non-Completed Booking
```
Given: Booking status = 'pending'
When: Customer tries to access rating form
Then:
  - Rating form not available
  - Message: "Can only rate after booking is completed"
```

#### Scenario: Submit Rating
```
Given: Booking completed (status='completed')
When: Customer goes to /customer/bookings/{id}/ratings
Then: Form displays:
  - Star rating selector (1-5)
  - Review textarea (max 1000 chars)
  - Technician info displayed
Then:
  - Customer selects 4 stars
  - Types: "Great work! Fixed our AC perfectly"
  - Submits
Then:
  - Rating saved
  - Technician average rating updated
  - Customer redirected to booking detail
  - Rating visible to all
```

#### Scenario: Duplicate Rating Prevention
```
Given: Customer already rated this booking
When: Customer tries to rate again
Then:
  - Message: "You already rated this booking"
  - Option to edit existing rating instead
```

#### Scenario: Edit Rating
```
Given: Rating already exists
When: Customer clicks "Edit Rating"
Then:
  - Form pre-filled with current data
  - Can change stars and review
  - Submit updates
  - Update timestamp recorded
```

#### Scenario: Delete Rating
```
Given: Customer on rating page
When: Customer clicks "Delete"
Then:
  - Confirmation dialog: "Sure?"
  - On confirm: Rating deleted
  - Technician average recalculated
```

---

## 2. TECHNICIAN ROLE TESTING

### 2.1 Registration & Profile
#### Scenario: Register as Technician
```
Given: User on /register/provider
When: User fills form:
  - name: Ahmad Teknisi
  - email: ahmad@provider.com
  - password: SecurePass123!
  - specialization: AC Repair
  - service_areas: Jakarta Selatan, Jakarta Pusat
And: Submit
Then:
  - Technician account created
  - role = 'technician'
  - Can login with /login/technician
  - Redirect to /technician/dashboard
```

#### Scenario: Update Profile
```
Given: Technician logged in
When: Goes to /profile/edit
And: Updates:
  - Name: "Ahmad Teknisi Pro"
  - Service areas: Added Tangerang
  - Experience: "15 tahun"
  - Certifications: "Samsung, LG"
And: Submit
Then:
  - Profile updated
  - Changes visible on public profile
```

---

### 2.2 Booking Management
#### Scenario: View Assigned Bookings
```
Given: Technician on /technician/bookings
Then: Show only bookings where:
  - technician_id = logged in technician
  - Status = pending, in_progress, or completed
  - Sorted by scheduled_date (nearest first)

Display shows:
  - Customer name
  - Service
  - Scheduled date/time
  - Location
  - Status badge
  - Action buttons
```

#### Scenario: View Booking Detail
```
Given: Technician clicks on booking
When: Goes to /technician/bookings/{id}
Then: Show:
  - Full booking details
  - Customer info & phone
  - Exact location/address
  - Service description
  - Customer notes
  - Current status
  - Invoice total
```

#### Scenario: Start Work
```
Given: Booking status = 'confirmed'
When: Technician ready to work
Then:
  - Status changes to 'in_progress'
  - Customer notified: "Technician started working"
  - Timestamp recorded
```

#### Scenario: Complete Booking
```
Given: Booking status = 'in_progress'
When: Technician clicks "Mark as Completed"
Then: Form shows:
  - Completion notes textarea
  - Issues encountered field
  - Photos upload (before/after)
  - Signature pad (optional)
And: Technician fills and submits
Then:
  - Status = 'completed'
  - Customer notified: "Work completed"
  - Customer can now rate
  - Invoice ready for payment
```

#### Scenario: Late Arrival
```
Given: Booking scheduled for 10:00 AM
When: Technician starts work at 11:30 AM
Then:
  - Still allowed to proceed
  - Late flag may be recorded (future feature)
  - Still can complete and customer can rate
```

---

### 2.3 Dashboard & Stats
#### Scenario: View Performance Stats
```
Given: Technician on /technician/dashboard
Then: Display:
  - Total bookings completed
  - Average rating (e.g., 4.5/5)
  - Active bookings (in_progress count)
  - Upcoming bookings (next 7 days)
  - Earnings this month
  - Busy/slow hours graph
```

#### Scenario: View Ratings
```
Given: Technician on dashboard or public profile
Then: Show:
  - All ratings received (newest first)
  - Star rating display
  - Customer review text
  - Date given
  - Response option (optional future feature)
```

---

## 3. ADMIN ROLE TESTING

### 3.1 User Management
#### Scenario: View All Customers
```
Given: Admin on /customers
Then: List with:
  - Pagination (15 per page)
  - Search by name/email
  - Filter (active, inactive, etc)
  - Total count
  - Actions (view, edit, delete)
```

#### Scenario: Edit Customer
```
Given: Admin clicks edit on customer
When: Admin changes:
  - Name: "John Smith"
  - Email: "john.smith@example.com"
  - Status: "active/inactive"
And: Submit
Then:
  - Customer data updated
  - Audit log recorded (who changed, when)
  - Customer notified (optional)
```

#### Scenario: Delete Customer
```
Given: Admin clicks delete on customer
When: Admin confirms deletion
Then:
  - Customer soft-deleted (deleted_at set)
  - Customer account deactivated
  - Can be restored later
  - Customer notified
```

---

### 3.2 Booking Management
#### Scenario: Assign Technician to Pending Booking
```
Given: Booking status = 'pending'
When: Admin clicks edit at /bookings/{id}/edit
Then: Form shows:
  - Service info (read-only)
  - Customer info (read-only)
  - Technician dropdown:
    - Shows available technicians
    - Filtered by service match
    - Shows current rating
    - Shows workload
And: Admin selects technician
And: Submits
Then:
  - technician_id assigned
  - Status = 'confirmed'
  - Notifications sent to customer & technician
```

#### Scenario: Cannot Assign Unavailable Technician (Future)
```
Given: Technician is marked as unavailable
When: Admin tries to assign
Then:
  - Technician grayed out in dropdown
  - Tooltip: "Unavailable from [date] to [date]"
```

#### Scenario: Mark Booking Completed (Force)
```
Given: Booking status = 'in_progress'
When: Admin clicks "Mark Completed" (override)
Then: Confirmation: "Force mark as completed?"
And: Reason required
Then:
  - Status = 'completed'
  - Action logged
  - Customer notified
  - Customer can rate (or already did)
```

---

### 3.3 Payment Approval
#### Scenario: Approve Payment Submission
```
Given: Payment status = 'pending' (awaiting approval)
When: Admin on /payments/pending
And: Admin clicks payment entry
Then: Shows:
  - Invoice details
  - Payment proof (screenshot)
  - Amount
  - Customer notes
  - Submission time
And: Admin reviews proof
And: Admin clicks "Approve"
Then:
  - Payment status = 'approved'
  - If auto-confirmed: Invoice marked paid
  - Notifications sent
```

#### Scenario: Reject Invalid Payment Proof
```
Given: Admin sees blurry/unclear proof
When: Admin clicks "Reject"
Then: Rejection reason form:
  - "Proof too blurry"
  - "Wrong amount shown"
  - Custom reason
And: Submit
Then:
  - Payment status stays 'pending'
  - Customer notified with reason
  - Customer can resubmit better proof
```

#### Scenario: Midtrans Auto-Confirmation
```
Given: Customer completed e-wallet payment
When: Midtrans webhook hits /webhooks/midtrans
Then:
  - Signature verified
  - Payment marked confirmed
  - Invoice auto-marked paid
  - Customer auto-notified
  - Admin sees as confirmed (no approval needed)
```

---

### 3.4 Reports
#### Scenario: Generate Revenue Report
```
Given: Admin on /reports/revenue
When: Admin selects:
  - Period: "Jan 2025 - Mar 2025"
  - Group by: "Service"
And: Clicks "Generate"
Then: Shows:
  - Revenue by service (table & chart)
  - Total revenue
  - Avg revenue per booking
  - Payment method breakdown
```

#### Scenario: View Technician Performance
```
Given: Admin on /reports/technicians
Then: Shows ranking:
  - Technician name
  - Total bookings
  - Completed bookings
  - Avg rating
  - Total earnings
  - Customer feedback
```

---

## 4. EDGE CASES & ERROR HANDLING

### 4.1 Concurrent Booking Issues
#### Scenario: Double Booking Prevention
```
Given: Customer has pending booking for tomorrow 10-12 AM
When: Customer tries to create another for tomorrow 10-11 AM
Then:
  - Warning: "Time conflict with existing booking"
  - Option: Cancel old or reschedule new
```

### 4.2 Payment Issues
#### Scenario: Payment Timeout
```
Given: Customer submits payment
When: Payment gateway times out
Then:
  - Error message: "Payment processing timeout"
  - Payment status stays 'pending'
  - Customer can retry
  - Retry attempt logged
```

#### Scenario: Multiple Payment Attempts
```
Given: Customer submits 3 different payments for same invoice
When: All are pending
Then:
  - Admin sees all 3 attempts
  - Admin approves only one
  - Others rejected/cancelled
  - Only one payment confirmed per invoice
```

### 4.3 Rating Issues
#### Scenario: Late Rating Submission
```
Given: Booking completed 6 months ago
When: Customer tries to rate now
Then:
  - Still allowed (no time limit)
  - Or enforced limit (30 days) - configurable
```

### 4.4 Authorization Issues
#### Scenario: Customer Access Other Customer's Booking
```
Given: Customer A tries to access Customer B's booking URL
When: Direct URL manipulation: /customer/bookings/999
Then:
  - 403 Forbidden error
  - "Unauthorized access"
  - Incident logged
```

#### Scenario: Technician Access Admin Panel
```
Given: Technician tries URL: /dashboard
When: This is admin-only route
Then:
  - 403 Forbidden
  - Redirect to /technician/dashboard
```

#### Scenario: Non-Authenticated User Access Routes
```
Given: Guest user tries: /customer/bookings
When: No session/auth
Then:
  - Redirect to /login
  - Store intended URL
  - After login, redirect to original URL
```

### 4.5 Data Consistency Issues
#### Scenario: Booking Without Technician Completion Request
```
Given: Booking marked completed
When: Before technician clicked complete
Then:
  - Still allowed if admin forced it
  - Log recorded
  - Technician may not have completion notes
```

#### Scenario: Delete Booking With Payments
```
Given: Booking has associated payments
When: Admin tries to delete booking
Then:
  - Prevent deletion
  - Message: "Cannot delete booking with payments"
  - Option: Cancel booking instead
```

---

## 5. LOAD TESTING SCENARIOS (Future)

### Scenario: Multiple Concurrent Bookings
```
Given: 100 customers create bookings simultaneously
When: Peak hour traffic
Then:
  - All bookings saved correctly
  - No data corruption
  - Performance acceptable (< 2 sec response)
  - Database locks handled
```

### Scenario: Large Report Generation
```
Given: Generate revenue report for 1 year
When: Data = 10,000+ bookings
Then:
  - Report generates in < 5 seconds
  - Memory usage acceptable
  - No timeout
```

---

## ✅ Testing Checklist

- [ ] Complete customer journey (register → book → pay → rate)
- [ ] Complete technician workflow (assign → start → complete)
- [ ] Complete admin flow (create → assign → approve → report)
- [ ] All role separation/authorization tests
- [ ] Payment approval workflow
- [ ] Rating system
- [ ] Dashboard statistics
- [ ] Report generation
- [ ] Error handling for all edge cases
- [ ] Performance under load
- [ ] Database data consistency
- [ ] OAuth integration

---

**Last Updated:** April 26, 2026
**Version:** 1.0 - Complete Testing Scenarios
