@extends('layouts.app')

@section('title', 'Status Pembayaran')

@section('content')
<style>
    .progress-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .progress-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .progress-header h1 {
        font-size: 32px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .progress-header p {
        font-size: 16px;
        opacity: 0.9;
    }

    /* Progress Bar */
    .progress-bar-container {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 30px;
        margin-bottom: 30px;
    }

    .progress-bar-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    .progress-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 30px;
        left: 50%;
        width: calc(100% - 30px);
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        z-index: 0;
    }

    .progress-step.completed::after {
        background: rgba(255, 255, 255, 0.8);
    }

    .progress-step-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: 3px solid rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .progress-step.completed .progress-step-icon {
        background: rgba(255, 255, 255, 0.8);
        border-color: white;
        color: #667eea;
    }

    .progress-step.active .progress-step-icon {
        background: white;
        border-color: white;
        color: #667eea;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .progress-step-label {
        font-size: 13px;
        text-align: center;
        max-width: 100px;
        opacity: 0.8;
    }

    .progress-step.completed .progress-step-label {
        opacity: 1;
        font-weight: 600;
    }

    .progress-step.active .progress-step-label {
        opacity: 1;
        font-weight: 600;
    }

    /* Main Content */
    .status-section {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ee4d2d;
        color: #222;
    }

    .status-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .status-info {
            grid-template-columns: 1fr;
        }
    }

    .info-item {
        padding: 15px;
        background: #f9f9f9;
        border-radius: 6px;
        border-left: 4px solid #ee4d2d;
    }

    .info-label {
        font-size: 12px;
        color: #999;
        margin-bottom: 5px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .info-value {
        font-size: 16px;
        color: #222;
        font-weight: 600;
    }

    /* Buyer Protection */
    .buyer-protection-card {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border-left: 5px solid #4caf50;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .buyer-protection-card.expiring {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
        border-left-color: #ff9800;
    }

    .buyer-protection-card.expired {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        border-left-color: #f44336;
    }

    .protection-title {
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .buyer-protection-card .protection-title {
        color: #2e7d32;
    }

    .buyer-protection-card.expiring .protection-title {
        color: #e65100;
    }

    .buyer-protection-card.expired .protection-title {
        color: #c62828;
    }

    .protection-text {
        font-size: 13px;
        line-height: 1.5;
    }

    .buyer-protection-card .protection-text {
        color: #558b2f;
    }

    .buyer-protection-card.expiring .protection-text {
        color: #6d4c41;
    }

    .buyer-protection-card.expired .protection-text {
        color: #b71c1c;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 20px;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        width: 2px;
        height: 40px;
        background: #e0e0e0;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f5f5f5;
        border: 2px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    .timeline-item.completed .timeline-icon {
        background: #4caf50;
        border-color: #4caf50;
        color: white;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-time {
        font-size: 12px;
        color: #999;
        margin-bottom: 4px;
    }

    .timeline-text {
        font-weight: 600;
        color: #222;
        margin-bottom: 4px;
    }

    .timeline-desc {
        font-size: 13px;
        color: #666;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .action-btn {
        flex: 1;
        padding: 12px;
        border: 1px solid #e0e0e0;
        background: white;
        border-radius: 6px;
        text-decoration: none;
        text-align: center;
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

    .auto-refresh-badge {
        display: inline-block;
        padding: 6px 12px;
        background: #e3f2fd;
        color: #1565c0;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 15px;
    }
</style>

<div class="container-lg" style="padding: 30px 0;">
    <!-- Header -->
    <div class="progress-container">
        <div class="progress-header">
            <h1>Status Pembayaran</h1>
            <p>Invoice {{ $payment->invoice->invoice_number }}</p>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar-container">
            <div class="progress-bar-wrapper">
                <!-- Step 1: Submitted -->
                <div class="progress-step completed">
                    <div class="progress-step-icon">✓</div>
                    <div class="progress-step-label">Diajukan</div>
                </div>

                <!-- Step 2: Review -->
                <div class="progress-step @if($payment->isPending()) active @else completed @endif">
                    <div class="progress-step-icon">@if($payment->isApproved() || $payment->isRejected())✓@else⏱@endif</div>
                    <div class="progress-step-label">Verifikasi</div>
                </div>

                <!-- Step 3: Approved -->
                <div class="progress-step @if($payment->isApproved()) completed active @endif">
                    <div class="progress-step-icon">@if($payment->isApproved())✓@else◯@endif</div>
                    <div class="progress-step-label">Diterima</div>
                </div>

                <!-- Step 4: Completed -->
                <div class="progress-step @if($payment->isApproved()) completed active @endif">
                    <div class="progress-step-icon">@if($payment->isApproved())✓@else◯@endif</div>
                    <div class="progress-step-label">Selesai</div>
                </div>
            </div>

            <!-- Progress Text -->
            <div style="text-align: center;">
                <strong style="font-size: 16px;">{{ $progress['message'] }}</strong>
                <div class="auto-refresh-badge">
                    <i class="bi bi-arrow-repeat"></i> Auto-refresh setiap 30 detik
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Payment Details -->
            <div class="status-section">
                <div class="section-title">
                    <i class="bi bi-receipt"></i> Detail Pembayaran
                </div>

                <div class="status-info">
                    <div class="info-item">
                        <div class="info-label">Jumlah Pembayaran</div>
                        <div class="info-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Diajukan</div>
                        <div class="info-value">{{ $payment->submitted_date->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">{!! $payment->getStatusBadgeHtml() !!}</div>
                    </div>
                </div>

                @if($payment->isApproved())
                    <div class="info-item" style="background: #e8f5e9; border-left-color: #4caf50; margin-bottom: 0;">
                        <div class="info-label" style="color: #2e7d32;">Tanggal Persetujuan</div>
                        <div class="info-value" style="color: #2e7d32;">{{ $payment->approved_date->format('d/m/Y H:i') }}</div>
                    </div>
                @elseif($payment->isRejected())
                    <div class="info-item" style="background: #ffebee; border-left-color: #f44336; margin-bottom: 0;">
                        <div class="info-label" style="color: #c62828;">Alasan Penolakan</div>
                        <div class="info-value" style="color: #c62828;">{{ $payment->notes ?? 'Tidak ada keterangan' }}</div>
                    </div>
                @endif
            </div>

            <!-- Buyer Protection -->
            @if($buyerProtection['status'] === 'active')
                <div class="buyer-protection-card">
                    <div class="protection-title">
                        <i class="bi bi-shield-check"></i> Perlindungan Pembeli Aktif
                    </div>
                    <div class="protection-text">
                        ✓ Pembayaran Anda dilindungi hingga <strong>{{ $buyerProtection['end_date']->format('d/m/Y') }}</strong> 
                        (<strong>{{ $buyerProtection['days_left'] }} hari lagi</strong>)
                        <br><br>
                        Jika ada masalah atau ketidakpuasan dengan layanan, Anda dapat mengajukan klaim perlindungan pembeli 
                        selama masa berlaku perlindungan.
                    </div>
                </div>
            @elseif($buyerProtection['status'] === 'expiring')
                <div class="buyer-protection-card expiring">
                    <div class="protection-title">
                        <i class="bi bi-exclamation-triangle"></i> Perlindungan Pembeli Akan Berakhir
                    </div>
                    <div class="protection-text">
                        ⚠ Periode perlindungan akan berakhir pada <strong>{{ $buyerProtection['end_date']->format('d/m/Y') }}</strong>
                        <br><br>
                        Jika ada masalah, segera ajukan klaim sebelum periode perlindungan berakhir.
                    </div>
                </div>
            @elseif($buyerProtection['status'] === 'expired')
                <div class="buyer-protection-card expired">
                    <div class="protection-title">
                        <i class="bi bi-info-circle"></i> Perlindungan Pembeli Telah Berakhir
                    </div>
                    <div class="protection-text">
                        Periode perlindungan pembeli untuk transaksi ini telah berakhir pada 
                        <strong>{{ $buyerProtection['end_date']->format('d/m/Y') }}</strong>
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            <div class="status-section">
                <div class="section-title">
                    <i class="bi bi-clock-history"></i> Riwayat Pembayaran
                </div>

                <div class="timeline">
                    <div class="timeline-item completed">
                        <div class="timeline-icon"><i class="bi bi-check"></i></div>
                        <div class="timeline-content">
                            <div class="timeline-time">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                            <div class="timeline-text">Pembayaran Diajukan</div>
                            <div class="timeline-desc">Pembayaran sebesar Rp {{ number_format($payment->amount, 0, ',', '.') }} berhasil diajukan</div>
                        </div>
                    </div>

                    @if($payment->isApproved())
                        <div class="timeline-item completed">
                            <div class="timeline-icon"><i class="bi bi-check"></i></div>
                            <div class="timeline-content">
                                <div class="timeline-time">{{ $payment->approved_date->format('d/m/Y H:i') }}</div>
                                <div class="timeline-text">Pembayaran Disetujui</div>
                                <div class="timeline-desc">
                                    @if($payment->approver)
                                        Disetujui oleh {{ $payment->approver->name }}
                                    @else
                                        Pembayaran telah diverifikasi dan diterima
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-icon"><i class="bi bi-check"></i></div>
                            <div class="timeline-content">
                                <div class="timeline-time">{{ $payment->invoice->paid_date->format('d/m/Y H:i') ?? 'Sekarang' }}</div>
                                <div class="timeline-text">Invoice Ditandai Lunas</div>
                                <div class="timeline-desc">Invoice {{ $payment->invoice->invoice_number }} telah ditandai sebagai lunas</div>
                            </div>
                        </div>
                    @elseif($payment->isRejected())
                        <div class="timeline-item completed">
                            <div class="timeline-icon" style="background: #ffebee; border-color: #f44336; color: #f44336;">
                                <i class="bi bi-x"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-time">{{ $payment->approved_date->format('d/m/Y H:i') }}</div>
                                <div class="timeline-text" style="color: #f44336;">Pembayaran Ditolak</div>
                                <div class="timeline-desc">{{ $payment->notes ?? 'Pembayaran ditolak oleh admin' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="timeline-item">
                            <div class="timeline-icon" style="animation: pulse 2s infinite;">⏱</div>
                            <div class="timeline-content">
                                <div class="timeline-time">Sedang Diproses</div>
                                <div class="timeline-text">Menunggu Verifikasi Admin</div>
                                <div class="timeline-desc">Pembayaran Anda sedang diverifikasi oleh admin. Biasanya memakan waktu 1-2 jam kerja.</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Invoice Summary -->
            <div class="status-section">
                <div class="section-title">
                    <i class="bi bi-file-text"></i> Ringkasan Invoice
                </div>

                <div class="status-info" style="grid-template-columns: 1fr;">
                    <div class="info-item">
                        <div class="info-label">Nomor Invoice</div>
                        <div class="info-value">{{ $payment->invoice->invoice_number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Layanan</div>
                        <div class="info-value">{{ $payment->invoice->booking->service->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Invoice</div>
                        <div class="info-value">Rp {{ number_format($payment->invoice->total, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status Invoice</div>
                        <div class="info-value">
                            @if($payment->invoice->status === 'paid')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-warning">Belum Lunas</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($payment->isApproved())
                    <a href="{{ route('payment.receipt', $payment) }}" class="action-btn primary">
                        <i class="bi bi-download"></i> Lihat Kwitansi
                    </a>
                @elseif($payment->isRejected())
                    <a href="{{ route('payment.form', $payment->invoice) }}" class="action-btn primary">
                        <i class="bi bi-arrow-repeat"></i> Ajukan Ulang
                    </a>
                @else
                    <button class="action-btn" onclick="location.reload()">
                        <i class="bi bi-arrow-repeat"></i> Refresh
                    </button>
                @endif

                <a href="{{ route('customer.dashboard') }}" class="action-btn">
                    <i class="bi bi-house"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-refresh status setiap 30 detik
    setInterval(function() {
        fetch("{{ route('payment.api-progress', $payment) }}")
            .then(response => response.json())
            .then(data => {
                // Update progress bar dan status
                if (data.payment_id === {{ $payment->id }}) {
                    // Status berubah dari pending ke approved
                    if (data.status === 'approved' && '{{ $payment->status }}' !== 'approved') {
                        // Reload halaman untuk menampilkan status terbaru
                        location.reload();
                    }
                }
            })
            .catch(error => console.log('Auto-refresh failed:', error));
    }, 30000); // 30 detik
</script>
@endsection
