# 🔄 Workflow & Status Transitions by Role

## 1️⃣ BOOKING STATUS WORKFLOW

### Visual Flow
```
┌─────────────────────────────────────────────────────────────┐
│                     BOOKING LIFECYCLE                        │
└─────────────────────────────────────────────────────────────┘

                          CUSTOMER CREATES
                                ↓
                          pending
                          (awaiting admin confirmation)
                                ↓
                          confirmed
                          (technician assigned, admin accepted)
                                ↓
                          in_progress
                          (technician started work)
                                ↓
                          completed
                          (work done, ready for rating)
                                ↓
                          [RATING ALLOWED]

ANY STATUS ──→ cancelled (dapat dibatalkan kapan saja)
```

### Status Transitions by Role

#### 🔵 CUSTOMER
| Action | From | To | Controller | Route |
|--------|------|----|----|-----|
| Create Booking | - | `pending` | CustomerBookingController | POST /customer/bookings |
| Edit Booking | `pending` | `pending` | CustomerBookingController | PUT /customer/bookings/{id} |
| Cancel Booking | Any | `cancelled` | CustomerBookingController | POST /customer/bookings/{id}/cancel |
| View Status | - | - | CustomerBookingController | GET /customer/bookings/{id} |

#### 🟠 TECHNICIAN
| Action | From | To | Controller | Route |
|--------|------|----|----|-----|
| View Assignment | `confirmed` | - | CustomerBookingController | GET /technician/bookings |
| Start Work | `confirmed` | `in_progress` | BookingController | (Auto/Manual) |
| Mark Complete | `in_progress` | `completed` | BookingController | POST /technician/bookings/{id}/mark-completed |

#### 🔴 ADMIN
| Action | From | To | Controller | Route |
|--------|------|----|----|-----|
| Create Booking | - | `pending` | BookingController | POST /bookings |
| Confirm & Assign | `pending` | `confirmed` | BookingController | PUT /bookings/{id} |
| Mark Completed | `in_progress` | `completed` | BookingController | POST /bookings/{id}/mark-completed |
| Cancel Booking | Any | `cancelled` | BookingController | POST /bookings/{id}/cancel |
| Edit Booking | Any | Same | BookingController | PUT /bookings/{id} |

---

## 2️⃣ PAYMENT STATUS WORKFLOW

### Visual Flow
```
┌──────────────────────────────────────────────────────────┐
│                    PAYMENT LIFECYCLE                      │
└──────────────────────────────────────────────────────────┘

INVOICE CREATED
      ↓
  pending
  (waiting for customer payment submission)
      ↓
  [Customer submits payment via form]
      ↓
  pending → approved (admin approves OR auto via webhook)
      ↓
  approved → confirmed (payment gateway confirmed)
      ↓
  [Invoice marked as paid]
```

### Payment Status Transitions

#### 🔵 CUSTOMER
| Action | From | To | Controller | Route |
|--------|------|----|----|-----|
| View Invoice | - | - | InvoiceController | GET /customer/invoices/{id} |
| Submit Payment | `pending` | `pending` | PaymentController | POST /customer/invoices/{id}/payment-submit |
| Check Status | - | - | PaymentController | GET /api/payments/{id}/status |
| Cancel Payment | `pending` | cancelled | PaymentController | DELETE /payments/{id}/cancel |
| View History | - | - | PaymentController | GET /customer/invoices/{id}/payment-history |

#### 🔴 ADMIN
| Action | From | To | Controller | Route |
|--------|------|----|----|-----|
| Approve Payment | `pending` | `approved` | PaymentController | POST /payments/{id}/approve |
| Reject Payment | `pending` | `pending` | PaymentController | POST /payments/{id}/reject |
| Mark Invoice Paid | Any | paid | InvoiceController | POST /invoices/{id}/mark-paid |
| View Payment | - | - | PaymentController | GET /payments/{id} |

#### 🤖 SYSTEM (Webhook)
| Action | From | To | Controller | Route |
|--------|------|----|----|-----|
| Midtrans Callback | `approved` | `confirmed` | PaymentWebhookController | POST /webhooks/midtrans |

---

## 3️⃣ BOOKING ASSIGNMENT WORKFLOW

### Flow Untuk Assignment Technician

```
┌────────────────────────────────────────────────┐
│         TECHNICIAN ASSIGNMENT PROCESS          │
└────────────────────────────────────────────────┘

CUSTOMER CREATES BOOKING
        ↓
    pending
    (No technician assigned yet)
        ↓
ADMIN VIEW BOOKING DETAIL
        ↓
ADMIN ASSIGN TECHNICIAN
(via edit form atau dropdown)
        ↓
    confirmed
    (Technician assigned, ready to work)
        ↓
TECHNICIAN VIEW IN /technician/bookings
        ↓
TECHNICIAN START WORK
        ↓
    in_progress
        ↓
TECHNICIAN COMPLETE
        ↓
    completed
        ↓
CUSTOMER CAN RATE
```

### Skenario Alternatif

#### Jika Technician Tidak Tersedia
```
Admin assigns Technician A
    ↓
❌ Technician A offline/unavailable?
    ↓
👉 Admin manually edit & change to Technician B
    ↓
✅ Reattempt assignment
```

---

## 4️⃣ RATING WORKFLOW

### Timeline
```
BOOKING COMPLETED
(status = 'completed')
        ↓
CUSTOMER CAN ACCESS RATING FORM
/customer/bookings/{booking}/ratings
        ↓
CUSTOMER FILL RATING (1-5 stars)
+ Optional review text
        ↓
RATING SAVED
        ↓
✅ VISIBLE ON:
   - Technician profile
   - Admin technician detail
   - Technician dashboard (average)
        ↓
CUSTOMER CAN EDIT RATING
PUT /ratings/{rating}
        ↓
CUSTOMER CAN DELETE RATING
DELETE /ratings/{rating}
```

### Rating Access Rules
```
Quien puede rating:
  ✅ Customers (after booking completed)
  ✅ Customers (edit own rating)
  ❌ Technician (cannot rate themselves)
  ❌ Admin (cannot rate directly)

Quien dapat lihat rating:
  ✅ All users (public ratings)
  ✅ Technician (lihat ratings sendiri)
  ✅ Admin (manage all ratings)
```

---

## 5️⃣ SPECIAL FEATURES & WORKFLOWS

### A. Invoice Generation Workflow
```
BOOKING COMPLETED
        ↓
SYSTEM AUTO-CREATE INVOICE
(with booking details, price, tax)
        ↓
INVOICE STATUS: unpaid
        ↓
CUSTOMER RECEIVE NOTIFICATION
        ↓
CUSTOMER SUBMIT PAYMENT
        ↓
ADMIN APPROVE/REJECT
        ↓
PAYMENT GATEWAY CONFIRM
        ↓
INVOICE STATUS: paid
```

### B. Customer Payment Submission Flow
```
CUSTOMER VIEW INVOICE
/customer/invoices/{invoice}
        ↓
CLICK "SUBMIT PAYMENT"
        ↓
FILL PAYMENT FORM
(amount, method, proof/receipt)
        ↓
SUBMIT
        ↓
PAYMENT STATUS: pending approval
        ↓
CUSTOMER WAIT FOR ADMIN APPROVAL
(or automatic via webhook)
        ↓
PAYMENT APPROVED ✅
        ↓
INVOICE MARKED PAID
```

### C. Technician Profile Build Up
```
TECHNICIAN COMPLETE BOOKING
        ↓
CUSTOMER GIVE RATING
        ↓
RATING ADDED TO TECHNICIAN PROFILE
        ↓
AVERAGE RATING UPDATED
        ↓
CUSTOMER BROWSE SERVICES
        ↓
SEE TOP-RATED TECHNICIANS
        ↓
MORE LIKELY TO BOOK WITH HIGH-RATED TECHNICIAN
```

---

## 6️⃣ ROLE-BASED VISIBILITY RULES

### Booking Visibility
| Role | Can See |
|------|---------|
| **Customer** | Own bookings only |
| **Technician** | Own assigned bookings |
| **Admin** | All bookings |

### Invoice Visibility
| Role | Can See |
|------|---------|
| **Customer** | Own invoices |
| **Technician** | - (cannot see) |
| **Admin** | All invoices |

### Payment Visibility
| Role | Can See |
|------|---------|
| **Customer** | Own payments |
| **Technician** | - (cannot see) |
| **Admin** | All payments |

### User Data Visibility
| Role | Can See |
|------|---------|
| **Customer** | Own profile |
| **Technician** | Own profile |
| **Admin** | All users |

### Technician Profile Visibility
| Role | Can See |
|------|---------|
| **Customer** | Public profile (name, rating, service) |
| **Technician** | Own full profile |
| **Admin** | Full profile for all |

---

## 7️⃣ NOTIFICATION TRIGGERS (Future Enhancement)

```
Booking Created
  → 🔔 Admin: New booking submitted
  
Booking Confirmed
  → 🔔 Customer: Booking confirmed, technician assigned
  → 🔔 Technician: New booking assigned
  
Booking In Progress
  → 🔔 Customer: Work started
  
Booking Completed
  → 🔔 Customer: Work complete, can rate now
  → 🔔 Customer: Invoice ready, payment needed
  
Payment Submitted
  → 🔔 Admin: Payment waiting approval
  
Payment Approved
  → 🔔 Customer: Payment approved, invoice paid
  
Rating Submitted
  → 🔔 Technician: New rating received
```

---

## 8️⃣ ERROR HANDLING & EDGE CASES

### Cannot Complete Booking If:
- Status is not `in_progress`
- No technician assigned
- Booking has been cancelled

### Cannot Rate If:
- Booking status is not `completed`
- Rating already exists for this booking
- User is not the booking customer

### Cannot Payment Submit If:
- Invoice not found
- Invoice already paid
- Invoice status is invalid

### Cannot Assign Technician If:
- Technician is already at max capacity
- Technician is not available (future: availability calendar)
- Technician skill doesn't match service type

---

**Last Updated:** April 26, 2026
**Version:** 1.0 - Complete Workflow Documentation
