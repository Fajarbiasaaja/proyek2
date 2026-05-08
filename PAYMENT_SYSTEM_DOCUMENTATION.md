# Payment System Documentation

## Overview
Sistem pembayaran dengan approval workflow dua-tingkat: **Customer Submit** → **Admin Approve/Reject**

---

## Architecture

### Database Schema
**Table: `payments`**
- `id` - Primary Key
- `invoice_id` - Foreign Key to invoices table
- `amount` - Decimal(10,2) - Amount dibayarkan (bisa partial payment)
- `payment_method` - ENUM: cash, bank_transfer, credit_card, e_wallet, check
- `reference_number` - TEXT - Nomor referensi bank/kartu (opsional untuk cash)
- `payment_proof` - STRING - Path ke file bukti pembayaran (struk, transfer)
- `submitted_date` - TIMESTAMP - Kapan customer submit pembayaran
- `status` - ENUM: 
  - `pending_approval` - Menunggu admin review
  - `approved` - Admin sudah approve → Invoice jadi "paid"
  - `rejected` - Admin tolak → Customer bisa submit ulang
- `approved_by` - Foreign Key to users (FK admin yang approve)
- `approved_date` - TIMESTAMP - Kapan approve/reject
- `notes` - TEXT - Catatan dari admin (alasan reject, dll)
- `created_at, updated_at` - System timestamps

### Relationships
```
Payment → Invoice (BelongsTo)
Payment → User (Approver, BelongsTo)
Invoice → Payments (HasMany)
Invoice → Approved Payments (HasMany filtered)
```

---

## User Flows

### Customer Payment Submission Flow

#### 1. View Invoice
- Customer membuka invoice yang belum dibayar
- Status: `issued` atau `overdue`
- Tombol: "Pay Now"

#### 2. Payment Form
```
URL: /customer/invoices/{invoice}/payment-form
METHOD: GET
CONTROLLER: PaymentController@showPaymentForm

Returns:
- Invoice total amount
- Invoice details (number, service, due date)
- Form fields:
  * Amount (prefill dengan invoice total, bisa kurang untuk partial)
  * Payment Method (select: cash, bank_transfer, credit_card, e_wallet, check)
  * Reference Number (required jika bukan cash)
  * Payment Proof (file upload - jpg/jpeg/png/pdf, max 5MB)
```

#### 3. Submit Payment
```
URL: /customer/invoices/{invoice}/payment-submit
METHOD: POST
CONTROLLER: PaymentController@submitPayment

Input:
{
  "amount": 500000.00,
  "payment_method": "bank_transfer",
  "reference_number": "TRF12345678",
  "payment_proof": <file upload>
}

Validation:
- amount: required, numeric, min: 0.01, max: invoice.total
- payment_method: required, in: cash,bank_transfer,credit_card,e_wallet,check
- reference_number: required_unless:payment_method,cash
- payment_proof: file, optional, mimes: jpg,jpeg,png,pdf, max: 5120

Processing:
1. Upload file ke storage/public/payments/payment-{timestamp}-{filename}
2. Create Payment record:
   - status: "pending_approval"
   - submitted_date: now()
   - payment_proof: (file path)
3. Send notification ke admin (pending approval)

Response: Redirect ke invoice dengan success message
"Pembayaran berhasil disubmit. Menunggu persetujuan admin."
```

#### 4. View Payment History
```
URL: /customer/invoices/{invoice}/payment-history
METHOD: GET
CONTROLLER: PaymentController@paymentHistory

Returns:
- List all payment attempts untuk invoice ini
- Columns:
  * Tanggal submit (submitted_date)
  * Jumlah (amount)
  * Method (payment_method)
  * No. Referensi (reference_number)
  * Status (badge: pending_approval/approved/rejected)
  * Catatan (jika rejected, tampilkan alasan dari notes)
- Actions:
  * Approved → No action
  * Pending → View detail / Batalkan untuk resubmit
  * Rejected → Resubmit payment form baru
```

#### 5. Cancel Payment (Pending Only)
```
URL: /customer/payments/{payment}/cancel
METHOD: DELETE
CONTROLLER: PaymentController@cancelPayment

Conditions:
- Only available jika status = "pending_approval"
- Admin dapat reject terlebih dahulu

Action:
- Delete payment_proof dari storage
- Delete Payment record
- Customer bisa submit payment baru

Response: Redirect dengan "Pembayaran dibatalkan"
```

---

### Admin Approval Flow

#### 1. Dashboard - Pending Payments
```
URL: /admin/payments/pending
METHOD: GET
CONTROLLER: PaymentController@listPendingPayments

Returns:
- Paginated list (10 per page) of payments with status = "pending_approval"
- Sorted by submitted_date DESC (newest first)
- Eager load: invoice.booking.customer

Columns:
* Invoice #
* Customer Name
* Amount
* Bank/Method
* Submitted Date
* Action: "Review" button

User:
- Admin melihat semua pending payments
- Click "Review" untuk lihat detail dan decide
```

#### 2. Payment Detail Review
```
URL: /admin/payments/{payment}
METHOD: GET
CONTROLLER: PaymentController@paymentDetail

Returns:
Payment Details:
- Amount, Method, Reference No
- Payment Proof: Display image/PDF atau link download
- Submitted Date & Time

Invoice Details:
- Invoice Number
- Service Description
- Invoice Total
- Current Payment Status

Customer Info:
- Name, Email, Phone
- Address
- Account Status

Buttons:
[ Approve ] [ Reject ]
```

#### 3. Approve Payment
```
URL: /admin/payments/{payment}/approve
METHOD: POST
CONTROLLER: PaymentController@approvePayment

Input (optional):
{
  "notes": "Pembayaran diterima - Transfer verified"
}

Processing:
1. Update Payment record:
   - status: "approved"
   - approved_by: auth()->id() (admin ID)
   - approved_date: now()
   - notes: (from input if provided)

2. Update related Invoice:
   - status: "paid"
   - paid_date: now()

3. Send notification to customer:
   - "Pembayaran Anda telah disetujui"
   - Email notification
   - Payment confirmed

Response: Back ke pending list dengan success message
"Pembayaran disetujui ✓"
```

#### 4. Reject Payment
```
URL: /admin/payments/{payment}/reject
METHOD: POST
CONTROLLER: PaymentController@rejectPayment

Input (required):
{
  "notes": "Referensi bank tidak cocok" (reason for rejection)
}

Processing:
1. Delete payment_proof file dari storage

2. Update Payment record:
   - status: "rejected"
   - approved_by: auth()->id()
   - approved_date: now()
   - notes: (reason from admin)

3. Invoice stays unpaid untuk resubmit

4. Send notification to customer:
   - "Pembayaran Anda ditolak"
   - Reason dari admin notes
   - "Silakan submit pembayaran baru"

Response: Back ke pending list dengan message
"Pembayaran ditolak. Customer notified."
```

---

## Database Queries

### Find Pending Payments for Review
```php
$pending = Payment::where('status', 'pending_approval')
    ->orderBy('submitted_date', 'desc')
    ->paginate(10);
```

### Get All Payments for Invoice
```php
$payments = Invoice::find($id)->payments()
    ->orderBy('submitted_date', 'desc')
    ->get();
```

### Get Approved Payments Only
```php
$approved = Invoice::find($id)->approvedPayments()->get();
// or
$paid = Payment::where('invoice_id', $id)
    ->where('status', 'approved')
    ->get();
```

### Customer Payment History
```php
$history = Payment::whereHas('invoice.booking.customer', fn($q) => 
    $q->where('customers.user_id', auth()->id())
)->get();
```

---

## Integration Points

### Invoice Model
```php
// Relationships
public function payments()   // All payment attempts
public function approvedPayments()  // Only approved

// Helpers
$invoice->payments()->count()  // Total attempts
$invoice->approvedPayments()->sum('amount')  // Total paid
$invoice->latestPayment()  // Last attempt
$invoice->isPaid()  // status == 'paid'
```

### Workflow Status
```
Invoice Status Transitions:
issued → pending_payment (after service done)
pending_payment → paid (after admin approve payment)
pending_payment → overdue (after due date)
overdue → paid (after payment approved)
```

---

## Files & Routes

### Routes
```
Customer Routes:
GET    /customer/invoices/{invoice}/payment-form
POST   /customer/invoices/{invoice}/payment-submit  
GET    /customer/invoices/{invoice}/payment-history
DELETE /customer/payments/{payment}/cancel

Admin Routes:
GET    /admin/payments/pending
GET    /admin/payments/{payment}
POST   /admin/payments/{payment}/approve
POST   /admin/payments/{payment}/reject
```

### Controllers
- `app/Http/Controllers/PaymentController.php` - 400+ lines

### Models
- `app/Models/Payment.php` - Payment model dengan scopes & relationships
- `app/Models/Invoice.php` - Updated dengan payment relationships

### Migrations
- `database/migrations/2025_02_18_120000_create_payments_table.php`

### Storage
- `storage/public/payments/` - Payment proof files
- Naming: `payment-{timestamp}-{filename}`

### Views (To Be Created)
- `resources/views/payments/payment-form.blade.php`
- `resources/views/payments/payment-history.blade.php`
- `resources/views/payments/admin-pending-list.blade.php`
- `resources/views/payments/admin-payment-detail.blade.php`

---

## Validation Rules

### Payment Submission
```php
$validated = $request->validate([
    'amount' => 'required|numeric|min:0.01|max:' . $invoice->total,
    'payment_method' => 'required|in:cash,bank_transfer,credit_card,e_wallet,check',
    'reference_number' => 'required_unless:payment_method,cash',
    'payment_proof' => 'file|mimes:jpg,jpeg,png,pdf|max:5120'
]);
```

### Admin Approval
```php
$validated = $request->validate([
    'notes' => 'nullable|string|max:500'
]);
```

### Admin Rejection
```php
$validated = $request->validate([
    'notes' => 'required|string|max:500'  // Reason required
]);
```

---

## Error Handling

### When Payment Rejected
- Customer notification sent with reason
- Payment record marked `rejected`
- Customer can submit new payment immediately
- Old proof file deleted from storage

### When File Upload Fails
- Validation error returned
- Customer told max file size is 5MB
- Allowed formats: JPG, PNG, PDF

### Duplicate Payment Prevention
- Each submit creates new Payment record
- Admin can reject and customer resubmit
- Only approved payment counts for invoice.paid_date

---

## Partial Payments

System supports partial payments:
```
Invoice Total: 2,000,000
First Payment: 500,000 (partial)
Second Payment: 1,500,000 (complete)

After first approved:
- Invoice status still "pending_payment"
- approved_payments total: 500,000

After second approved:
- Invoice status changes to "paid"
- paid_date set
```

---

## Notifications Development

### Future Enhancement: Email Notifications
```php
// When payment submitted
Mail::to($admin->email)->queue(new PaymentSubmittedNotification($payment));

// When payment approved
Mail::to($customer->email)->queue(new PaymentApprovedNotification($payment));

// When payment rejected
Mail::to($customer->email)->queue(new PaymentRejectedNotification($payment));
```

---

## Testing Checklist

- [ ] Customer can submit payment with all required fields
- [ ] File upload respects 5MB limit and allowed formats
- [ ] Admin can view all pending payments
- [ ] Admin can approve payment → Invoice marked paid
- [ ] Admin can reject payment with reason → Customer notified
- [ ] Customer can see payment history with all attempts
- [ ] Customer cannot submit payment > invoice total (validation)
- [ ] Partial payments work correctly (multiple payments per invoice)
- [ ] Payment proof files stored correctly in storage/public/payments/
- [ ] Cascade delete: deleting invoice also deletes payments
- [ ] Email notifications sent on approve/reject

---

## Performance Considerations

### Eager Loading
```php
// Bad: N+1 queries
Payment::all()->each(fn($p) => $p->invoice->customer->name);

// Good: eager load
Payment::with('invoice.booking.customer')->get();
```

### Pagination
- "/admin/payments/pending" uses pagination (10 per page)
- Prevents loading 10,000+ records at once

### Indexing
- Consider adding index on payments.status
- Consider adding index on payments.invoice_id

---

## Config Reference

**Payment Methods Available:**
1. `cash` - Pembayaran tunai ke lokasi
2. `bank_transfer` - Transfer bank ke rekening perusahaan
3. `credit_card` - Kartu kredit (jika ada payment gateway)
4. `e_wallet` - E-wallet / mobile payment
5. `check` - Cek / giro

---

See [OAUTH_SETUP_GUIDE.md](./OAUTH_SETUP_GUIDE.md) for authentication setup.

For application overview, see [APLIKASI_AC_README.md](./APLIKASI_AC_README.md).
