@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<style>
    .history-container {
        padding: 30px 0;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #222;
    }

    .page-header p {
        color: #666;
        font-size: 14px;
    }

    .invoice-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .invoice-summary-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    @media (max-width: 768px) {
        .invoice-summary-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .summary-item {
        text-align: center;
    }

    .summary-label {
        font-size: 12px;
        opacity: 0.9;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 700;
    }

    .payment-list {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .payment-item {
        border-bottom: 1px solid #f5f5f5;
        padding: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .payment-item:hover {
        background: #f9f9f9;
    }

    .payment-item:last-child {
        border-bottom: none;
    }

    .payment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .payment-amount {
        font-size: 18px;
        font-weight: 700;
        color: #ee4d2d;
    }

    .payment-status {
        display: inline-block;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-warning {
        background: #fffbea;
        color: #ff9c00;
    }

    .badge-success {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .badge-danger {
        background: #ffebee;
        color: #c62828;
    }

    .payment-meta {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
    }

    .payment-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .payment-method {
        display: inline-block;
        padding: 4px 10px;
        background: #f5f5f5;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .payment-details {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 12px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    @media (max-width: 768px) {
        .payment-details {
            grid-template-columns: 1fr;
        }
    }

    .detail-item {
        font-size: 12px;
    }

    .detail-label {
        color: #999;
        margin-bottom: 4px;
    }

    .detail-value {
        font-weight: 600;
        color: #222;
    }

    .buyer-protection-badge {
        display: inline-block;
        padding: 6px 12px;
        background: #e8f5e9;
        color: #2e7d32;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 8px;
    }

    .buyer-protection-badge.expiring {
        background: #fff3e0;
        color: #ff9c00;
    }

    .buyer-protection-badge.expired {
        background: #ffebee;
        color: #c62828;
    }

    .rejection-reason {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        border-left: 4px solid #f44336;
        padding: 12px;
        border-radius: 4px;
        margin-top: 12px;
        font-size: 12px;
        color: #c62828;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .action-btn {
        padding: 8px 16px;
        border: 1px solid #e0e0e0;
        background: white;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #222;
    }

    .action-btn:hover {
        border-color: #ee4d2d;
        color: #ee4d2d;
        background: #fff9f7;
    }

    .action-btn.danger {
        border-color: #f44336;
        color: #f44336;
    }

    .action-btn.danger:hover {
        background: #ffebee;
    }

    .action-btn.primary {
        background: #ee4d2d;
        color: white;
        border-color: #ee4d2d;
    }

    .action-btn.primary:hover {
        background: #d63821;
        border-color: #d63821;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
    }

    .empty-state-text {
        color: #999;
        margin-bottom: 20px;
    }

    .empty-state-btn {
        display: inline-block;
        padding: 12px 24px;
        background: #ee4d2d;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
    }
</style>

<div class="container-lg history-container">
    <!-- Header -->
    <div class="page-header">
        <h1><i class="bi bi-receipt"></i> Riwayat Pembayaran</h1>
        <p>Invoice {{ $invoice->invoice_number }} - {{ $invoice->booking->service->name }}</p>
    </div>

    <!-- Invoice Summary -->
    <div class="invoice-summary">
        <div class="invoice-summary-row">
            <div class="summary-item">
                <div class="summary-label">Total Invoice</div>
                <div class="summary-value">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Sudah Dibayar</div>
                <div class="summary-value">
                    Rp {{ number_format($payments->where('status', 'approved')->sum('amount'), 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Sisa Pembayaran</div>
                <div class="summary-value">
                    Rp {{ number_format($invoice->total - $payments->where('status', 'approved')->sum('amount'), 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Status</div>
                <div class="summary-value">
                    @if($invoice->status === 'paid')
                        <span style="color: #4caf50;">✓ Lunas</span>
                    @else
                        <span style="color: #ff9c00;">⚠ Belum Lunas</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment List -->
    @if($payments->count() > 0)
        <div class="payment-list">
            @foreach($payments as $payment)
                <div class="payment-item">
                    <!-- Header -->
                    <div class="payment-header">
                        <div>
                            <div class="payment-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                            <div class="payment-meta">
                                <div class="payment-meta-item">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ $payment->submitted_date->format('d/m/Y H:i') }}
                                </div>
                                <div class="payment-meta-item">
                                    <span class="payment-method">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="payment-status">
                            @if($payment->status === 'pending_approval')
                                <span class="badge badge-warning">
                                    <i class="bi bi-clock"></i> Verifikasi
                                </span>
                            @elseif($payment->status === 'approved')
                                <span class="badge badge-success">
                                    <i class="bi bi-check-circle"></i> Diterima
                                </span>
                            @elseif($payment->status === 'rejected')
                                <span class="badge badge-danger">
                                    <i class="bi bi-x-circle"></i> Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="payment-details">
                        <div class="detail-item">
                            <div class="detail-label">ID Pembayaran</div>
                            <div class="detail-value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Referensi</div>
                            <div class="detail-value">{{ $payment->reference_number ?? '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Gateway</div>
                            <div class="detail-value">{{ ucfirst($payment->payment_gateway ?? 'Manual') }}</div>
                        </div>
                    </div>

                    <!-- Buyer Protection -->
                    @if($payment->isApproved())
                        @php
                            $protectionInfo = $payment->getBuyerProtectionInfo();
                        @endphp
                        @if($protectionInfo['status'] === 'active')
                            <div class="buyer-protection-badge">
                                <i class="bi bi-shield-check"></i> 
                                Dilindungi hingga {{ $protectionInfo['end_date']->format('d/m/Y') }} 
                                ({{ $protectionInfo['days_left'] }} hari)
                            </div>
                        @elseif($protectionInfo['status'] === 'expiring')
                            <div class="buyer-protection-badge expiring">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Proteksi akan berakhir {{ $protectionInfo['end_date']->format('d/m/Y') }}
                            </div>
                        @else
                            <div class="buyer-protection-badge expired">
                                <i class="bi bi-info-circle"></i> 
                                Proteksi berakhir pada {{ $protectionInfo['end_date']->format('d/m/Y') }}
                            </div>
                        @endif
                    @endif

                    <!-- Rejection Reason -->
                    @if($payment->isRejected())
                        <div class="rejection-reason">
                            <strong>Alasan Penolakan:</strong><br>
                            {{ $payment->notes ?? 'Tidak ada keterangan' }}
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        @if($payment->isApproved())
                            <a href="{{ route('payment.receipt', $payment) }}" class="action-btn primary">
                                <i class="bi bi-receipt"></i> Lihat Kwitansi
                            </a>
                        @elseif($payment->isPending())
                            <a href="{{ route('payment.progress', $payment) }}" class="action-btn">
                                <i class="bi bi-hourglass-split"></i> Lihat Progress
                            </a>
                            <form action="{{ route('payment.cancel', $payment) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn danger" onclick="return confirm('Batalkan pembayaran ini?')">
                                    <i class="bi bi-trash"></i> Batalkan
                                </button>
                            </form>
                        @elseif($payment->isRejected())
                            <a href="{{ route('payment.form', $invoice) }}" class="action-btn primary">
                                <i class="bi bi-arrow-repeat"></i> Ajukan Ulang
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">💳</div>
            <div class="empty-state-text">
                Belum ada riwayat pembayaran untuk invoice ini
            </div>
            <a href="{{ route('payment.form', $invoice) }}" class="empty-state-btn">
                <i class="bi bi-plus-circle"></i> Ajukan Pembayaran
            </a>
        </div>
    @endif

    <!-- Back Button -->
    <div style="margin-top: 30px; text-align: center;">
        <a href="{{ route('invoice.show', $invoice) }}" style="color: #ee4d2d; text-decoration: none; font-weight: 600;">
            <i class="bi bi-arrow-left"></i> Kembali ke Invoice
        </a>
    </div>
</div>
@endsection
