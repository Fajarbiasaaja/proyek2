<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model - Payment Transaction Records
 * 
 * Represents individual payment transactions dari customer untuk settle invoices.
 * Dengan approval workflow dari admin untuk validation & audit purposes.
 * 
 * Table: payments
 * 
 * @property int $id - Primary key
 * @property int $invoice_id - Foreign key ke invoice yang dibayar
 * @property decimal $amount - Nominal pembayaran (bisa partial)
 * @property string $payment_method - Metode: cash, bank_transfer, credit_card, e_wallet, check
 * @property string|null $reference_number - Reference: bank ref, check number, CC ID
 * @property string|null $payment_proof - File path ke bukti pembayaran
 * @property datetime|null $submitted_date - Saat customer submit payment
 * @property string $status - enum: pending_approval, approved, rejected
 * @property int|null $approved_by - User ID admin yang approve/reject
 * @property datetime|null $approved_date - Saat approval/rejection
 * @property text|null $notes - Admin notes (confirmation atau rejection reason)
 * @property string|null $payment_gateway - Payment gateway: midtrans, manual
 * @property string|null $transaction_id - Unique transaction ID dari Midtrans
 * @property json|null $gateway_response - Full response dari payment gateway
 * @property string|null $gateway_status - Status dari gateway (pending, settlement, etc)
 * @property string|null $payment_url - URL untuk redirect ke payment gateway
 * @property datetime $created_at - Payment submission timestamp
 * @property datetime $updated_at - Last modification timestamp
 * 
 * Relationships:
 * - invoice() belongsTo: Invoice yang dibayar this payment
 * - approver() belongsTo User: Admin yang approve/reject this payment
 * 
 * Query Methods:
 * - pending(): Get pending_approval payments
 * - approved(): Get approved payments
 * - rejected(): Get rejected payments
 * - byInvoice($invoiceId): Filter by invoice
 * 
 * Status Helpers:
 * - isPending(): Check status = pending_approval
 * - isApproved(): Check status = approved
 * - isRejected(): Check status = rejected
 * 
 * Payment Workflow:
 * 1. Customer submit payment -> status = pending_approval
 * 2. Admin review -> approve atau reject
 * 3. If approved:
 *    - status = approved
 *    - approved_by = admin ID
 *    - approved_date = now()
 *    - Invoice updated: status=paid, paid_date=now()
 * 4. If rejected:
 *    - status = rejected
 *    - notes = rejection reason
 *    - Customer resubmit new payment
 * 
 * Casting:
 * - amount: Float/Decimal casting untuk currency calculation
 * - submitted_date: DateTime untuk date operation
 * - approved_date: DateTime untuk date operation
 * - status: String enum validation
 * 
 * Mass Assignment:
 * - Fillable untuk: amount, payment_method, reference_number, payment_proof, submitted_date, notes
 * - Protected: id, invoice_id, status, approved_by, approved_date (set via business logic)
 */
class Payment extends Model
{
    /**
     * Fillable fields untuk mass assignment
     * 
     * Customer dapat submit:
     * - amount: payment amount
     * - payment_method: chosen method
     * - reference_number: bank/check/CC reference
     * - payment_proof: upload file path
     * - submitted_date: timestamp
     * - notes: payment note/description
     * 
     * Gateway fields (filled by system):
     * - payment_gateway: gateway identifier
     * - transaction_id: gateway transaction ID
     * - gateway_response: full gateway response
     * - gateway_status: gateway payment status
     * - payment_url: redirect URL untuk payment
     * 
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'payment_gateway',
        'reference_number',
        'payment_proof',
        'submitted_date',
        'status',
        'approved_by',
        'approved_date',
        'notes',
        'transaction_id',
        'gateway_response',
        'gateway_status',
        'payment_url',
    ];

    /**
     * Casts untuk type conversion
     * 
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',          // Currency dengan 2 desimal
        'submitted_date' => 'datetime',   // DateTime untuk date operations
        'approved_date' => 'datetime',    // DateTime untuk date operations
        'gateway_response' => 'json',     // JSON casting untuk gateway response
    ];

    /**
     * Relationship: Invoice yang dibayar
     * 
     * One Payment belongs to One Invoice
     * Satu payment untuk settle satu invoice (atau partial)
     * 
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relationship: Admin user yang approve/reject payment
     * 
     * One Payment approved by One User (admin)
     * Relation ke User untuk audit trail
     * 
     * @return BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Query Scope: Get pending approval payments
     * 
     * Usage: Payment::pending()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Query Scope: Get approved payments
     * 
     * Usage: Payment::approved()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Query Scope: Get rejected payments
     * 
     * Usage: Payment::rejected()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Status Helper: Check if payment pending approval
     * 
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Status Helper: Check if payment approved
     * 
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Status Helper: Check if payment rejected
     * 
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get payment progress status (for UI progress indicator)
     * 
     * Mengembalikan progress status untuk UI:
     * 1. Pending Verification (0%) - Payment submitted, waiting admin review
     * 2. Under Review (33%) - Admin reviewing payment
     * 3. Verified (66%) - Admin verified, payment approved
     * 4. Completed (100%) - Invoice marked as paid
     * 5. Failed (0%) - Payment rejected
     * 
     * @return array ['status' => string, 'percentage' => int, 'message' => string, 'icon' => string]
     */
    public function getProgressStatus(): array
    {
        return match($this->status) {
            'pending_approval' => [
                'status' => 'pending_verification',
                'percentage' => 33,
                'message' => 'Menunggu verifikasi admin',
                'icon' => 'clock',
                'color' => 'warning',
            ],
            'approved' => [
                'status' => 'verified',
                'percentage' => 100,
                'message' => 'Pembayaran telah diterima',
                'icon' => 'check-circle',
                'color' => 'success',
            ],
            'rejected' => [
                'status' => 'failed',
                'percentage' => 0,
                'message' => 'Pembayaran ditolak',
                'icon' => 'x-circle',
                'color' => 'danger',
            ],
            default => [
                'status' => 'unknown',
                'percentage' => 0,
                'message' => 'Status tidak diketahui',
                'icon' => 'question-circle',
                'color' => 'secondary',
            ],
        };
    }

    /**
     * Get buyer protection info
     * 
     * Mengembalikan informasi proteksi pembeli:
     * - Protected duration (protection valid sampai kapan)
     * - Protection status (active/expired/completed)
     * - Dispute window (berapa lama buyer bisa buat dispute)
     * 
     * @return array
     */
    public function getBuyerProtectionInfo(): array
    {
        $approvedDate = $this->approved_date ?? $this->created_at;
        $protectionEndDate = $approvedDate->copy()->addDays(30); // 30 hari proteksi
        $now = now();

        if ($this->isApproved() && $now < $protectionEndDate) {
            $daysLeft = $now->diffInDays($protectionEndDate);
            return [
                'status' => 'active',
                'message' => "Dilindungi hingga $daysLeft hari lagi",
                'end_date' => $protectionEndDate,
                'is_protected' => true,
                'days_left' => $daysLeft,
            ];
        } elseif ($this->isApproved()) {
            return [
                'status' => 'expired',
                'message' => 'Periode proteksi telah berakhir',
                'end_date' => $protectionEndDate,
                'is_protected' => false,
                'days_left' => 0,
            ];
        } else {
            return [
                'status' => 'not_applicable',
                'message' => 'Proteksi berlaku setelah pembayaran disetujui',
                'end_date' => null,
                'is_protected' => false,
                'days_left' => 0,
            ];
        }
    }

    /**
     * Get formatted transaction status untuk display
     * 
     * @return string HTML badge
     */
    public function getStatusBadgeHtml(): string
    {
        return match($this->status) {
            'pending_approval' => '<span class="badge bg-warning"><i class="bi bi-clock"></i> Verifikasi</span>',
            'approved' => '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Diterima</span>',
            'rejected' => '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Gateway Helper: Check if payment menggunakan Midtrans gateway
     * 
     * @return bool
     */
    public function isMidtrans(): bool
    {
        return $this->payment_gateway === 'midtrans';
    }

    /**
     * Gateway Helper: Check if payment masih pending di gateway
     * 
     * @return bool
     */
    public function isGatewayPending(): bool
    {
        return in_array($this->gateway_status, ['pending', 'challenge']);
    }

    /**
     * Gateway Helper: Check if payment sudah settlement
     * 
     * @return bool
     */
    public function isGatewaySettled(): bool
    {
        return $this->gateway_status === 'settlement';
    }

    /**
     * Gateway Helper: Check if payment gagal di gateway
     * 
     * @return bool
     */
    public function isGatewayFailed(): bool
    {
        return in_array($this->gateway_status, ['failure', 'deny', 'cancel']);
    }
}
