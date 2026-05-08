@extends('layouts.app')

@section('title', 'Kwitansi Pembayaran')

@section('content')
<style>
    .receipt-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
    }

    .receipt-paper {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #ee4d2d;
    }

    .receipt-logo {
        font-size: 24px;
        font-weight: 700;
        color: #ee4d2d;
        margin-bottom: 10px;
    }

    .receipt-title {
        font-size: 18px;
        font-weight: 600;
        color: #222;
        margin-bottom: 5px;
    }

    .receipt-date {
        font-size: 12px;
        color: #999;
    }

    .receipt-content {
        margin-bottom: 30px;
    }

    .receipt-section {
        margin-bottom: 25px;
    }

    .receipt-section-title {
        font-weight: 700;
        color: #222;
        margin-bottom: 10px;
        font-size: 13px;
        text-transform: uppercase;
        color: #666;
    }

    .receipt-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }

    .receipt-row-label {
        color: #666;
    }

    .receipt-row-value {
        font-weight: 500;
        color: #222;
        text-align: right;
    }

    .receipt-row.total {
        border-bottom: 2px solid #ee4d2d;
        border-top: 2px solid #ee4d2d;
        padding: 12px 0;
        font-weight: 700;
        font-size: 16px;
    }

    .receipt-row.total .receipt-row-value {
        color: #ee4d2d;
    }

    .receipt-status {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border-left: 4px solid #4caf50;
        padding: 15px;
        border-radius: 4px;
        margin: 25px 0;
    }

    .receipt-status-title {
        font-weight: 600;
        color: #2e7d32;
        margin-bottom: 5px;
    }

    .receipt-status-text {
        font-size: 13px;
        color: #558b2f;
    }

    .receipt-footer {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
        font-size: 11px;
        color: #999;
    }

    .receipt-qr {
        text-align: center;
        margin: 20px 0;
    }

    .receipt-qr-code {
        display: inline-block;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 4px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 30px;
    }

    .action-btn {
        padding: 12px 24px;
        border: 1px solid #e0e0e0;
        background: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #222;
    }

    .action-btn:hover {
        border-color: #ee4d2d;
        color: #ee4d2d;
        background: #fff9f7;
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

    @media print {
        body {
            background: white;
        }
        .receipt-container {
            margin: 0;
            padding: 0;
        }
        .action-buttons {
            display: none;
        }
        .receipt-paper {
            box-shadow: none;
            border: none;
        }
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .receipt-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .receipt-info-box {
        padding: 15px;
        background: #f9f9f9;
        border-radius: 4px;
    }

    .receipt-info-label {
        font-size: 12px;
        color: #999;
        margin-bottom: 5px;
    }

    .receipt-info-value {
        font-weight: 600;
        font-size: 14px;
        color: #222;
    }
</style>

<div class="receipt-container">
    <div class="receipt-paper">
        <!-- Header -->
        <div class="receipt-header">
            <div class="receipt-logo">🎟️ JASA SERVIS AC</div>
            <div class="receipt-title">Kwitansi Pembayaran</div>
            <div class="receipt-date">{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>

        <!-- Content -->
        <div class="receipt-content">
            <!-- Status -->
            <div class="receipt-status">
                <div class="receipt-status-title">
                    ✓ Pembayaran Berhasil Diterima
                </div>
                <div class="receipt-status-text">
                    Terima kasih! Pembayaran Anda telah berhasil diverifikasi dan diterima.
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="receipt-section">
                <div class="receipt-section-title">Detail Transaksi</div>
                
                <div class="receipt-info-grid">
                    <div class="receipt-info-box">
                        <div class="receipt-info-label">ID Pembayaran</div>
                        <div class="receipt-info-value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="receipt-info-box">
                        <div class="receipt-info-label">Nomor Invoice</div>
                        <div class="receipt-info-value">{{ $payment->invoice->invoice_number }}</div>
                    </div>
                    <div class="receipt-info-box">
                        <div class="receipt-info-label">Tanggal Pembayaran</div>
                        <div class="receipt-info-value">{{ $payment->submitted_date->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="receipt-info-box">
                        <div class="receipt-info-label">Tanggal Persetujuan</div>
                        <div class="receipt-info-value">{{ $payment->approved_date->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="receipt-section">
                <div class="receipt-section-title">Informasi Pelanggan</div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Nama</span>
                    <span class="receipt-row-value">{{ $payment->invoice->booking->customer->name }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Email</span>
                    <span class="receipt-row-value">{{ $payment->invoice->booking->customer->email }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">No. Telepon</span>
                    <span class="receipt-row-value">{{ $payment->invoice->booking->customer->phone ?? '-' }}</span>
                </div>
            </div>

            <!-- Service Info -->
            <div class="receipt-section">
                <div class="receipt-section-title">Informasi Layanan</div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Tipe Layanan</span>
                    <span class="receipt-row-value">{{ $payment->invoice->booking->service->name }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Tanggal Layanan</span>
                    <span class="receipt-row-value">{{ $payment->invoice->booking->scheduled_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Lokasi</span>
                    <span class="receipt-row-value">{{ $payment->invoice->booking->location ?? '-' }}</span>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="receipt-section">
                <div class="receipt-section-title">Detail Pembayaran</div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Subtotal</span>
                    <span class="receipt-row-value">Rp {{ number_format($payment->invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Pajak (PPN)</span>
                    <span class="receipt-row-value">Rp {{ number_format($payment->invoice->tax, 0, ',', '.') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Metode Pembayaran</span>
                    <span class="receipt-row-value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Referensi</span>
                    <span class="receipt-row-value">{{ $payment->reference_number ?? '-' }}</span>
                </div>

                <!-- Total -->
                <div class="receipt-row total">
                    <span class="receipt-row-label">Total Pembayaran</span>
                    <span class="receipt-row-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Verification -->
            <div class="receipt-section">
                <div class="receipt-section-title">Verifikasi</div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Diverifikasi Oleh</span>
                    <span class="receipt-row-value">
                        @if($payment->approver)
                            {{ $payment->approver->name }} (Admin)
                        @else
                            Sistem
                        @endif
                    </span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Status</span>
                    <span class="receipt-row-value">
                        <span class="badge badge-success">✓ Verified</span>
                    </span>
                </div>
            </div>

            <!-- Buyer Protection -->
            <div class="receipt-section">
                <div class="receipt-section-title">Perlindungan Pembeli</div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Status Proteksi</span>
                    <span class="receipt-row-value">Aktif hingga {{ \Carbon\Carbon::parse($payment->approved_date)->addDays(30)->format('d/m/Y') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Durasi Proteksi</span>
                    <span class="receipt-row-value">30 hari dari persetujuan</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="receipt-footer">
                <p>
                    Kwitansi ini adalah bukti pembayaran yang sah. Silakan simpan untuk referensi Anda.<br>
                    Jika ada pertanyaan, hubungi layanan pelanggan kami di support@jasaservisac.com
                </p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="action-btn" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak Kwitansi
        </button>
        <a href="{{ route('payment.download-receipt', $payment) }}" class="action-btn primary">
            <i class="bi bi-download"></i> Download PDF
        </a>
        <a href="{{ route('customer.dashboard') }}" class="action-btn">
            <i class="bi bi-house"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<script>
    // Allow printing
    function printReceipt() {
        window.print();
    }
</script>
@endsection
