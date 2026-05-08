@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
    <div class="page-title">
        <i class="bi bi-credit-card"></i>
        <h1>Detail Pembayaran #PM{{ $payment->id }}</h1>
    </div>

    <div class="row">
        <!-- Payment Detail Section -->
        <div class="col-lg-8">
            <!-- Payment Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">ID Pembayaran</label>
                                <p><strong>#PM{{ $payment->id }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Jumlah Pembayaran</label>
                                <p><strong class="text-success" style="font-size: 1.3rem;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Metode Pembayaran</label>
                                <p>
                                    @php
                                        $methodLabel = [
                                            'cash' => 'Tunai',
                                            'bank_transfer' => 'Transfer Bank',
                                            'e_wallet' => 'E-Wallet',
                                            'credit_card' => 'Kartu Kredit'
                                        ];
                                        $methodColor = [
                                            'cash' => 'success',
                                            'bank_transfer' => 'info',
                                            'e_wallet' => 'warning',
                                            'credit_card' => 'primary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $methodColor[$payment->payment_method] ?? 'secondary' }}">
                                        {{ $methodLabel[$payment->payment_method] ?? $payment->payment_method }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Nomor Referensi</label>
                                <p><strong>{{ $payment->reference_number ?? '(Tidak ada)' }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Submit</label>
                                <p><strong>{{ $payment->submitted_date->format('d/m/Y H:i') }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <p>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Proof Section -->
            @if($payment->payment_proof)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-image"></i> Bukti Pembayaran</h5>
                    </div>
                    <div class="card-body" style="text-align: center;">
                        @php
                            $ext = strtolower(pathinfo($payment->payment_proof, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset('storage/' . $payment->payment_proof) }}" 
                                 alt="Bukti Pembayaran" 
                                 style="max-width: 100%; max-height: 500px; border-radius: 8px;">
                        @elseif($ext === 'pdf')
                            <div style="padding: 30px; background: #f8f9fa; border-radius: 8px; border: 2px dashed #dee2e6;">
                                <i class="bi bi-file-pdf" style="font-size: 3rem; color: #dc3545;"></i>
                                <p class="mt-2">File PDF: {{ basename($payment->payment_proof) }}</p>
                                <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="btn btn-sm btn-danger">
                                    <i class="bi bi-download"></i> Lihat PDF
                                </a>
                            </div>
                        @else
                            <div style="padding: 30px; background: #f8f9fa; border-radius: 8px;">
                                <p>Format file tidak ditampilkan</p>
                                <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="bi bi-download"></i> Download File
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card mb-3">
                    <div class="card-body" style="padding: 30px; text-align: center; background: #fff3cd;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: #856404;"></i>
                        <p class="mt-2 text-muted"><strong>Tidak ada bukti pembayaran</strong></p>
                    </div>
                </div>
            @endif

            <!-- Notes Section -->
            @if($payment->notes)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Catatan Customer</h5>
                    </div>
                    <div class="card-body">
                        <p style="white-space: pre-wrap;">{{ $payment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Approval Section -->
        <div class="col-lg-4">
            <!-- Invoice Detail Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-receipt"></i> Detail Invoice</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">No Invoice</label>
                        <p><strong>{{ $payment->invoice->invoice_number }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Total Invoice</label>
                        <p><strong>Rp {{ number_format($payment->invoice->total, 0, ',', '.') }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Layanan</label>
                        <p><strong>{{ $payment->invoice->booking->service->name }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Tanggal Service</label>
                        <p><strong>{{ $payment->invoice->booking->scheduled_date->format('d/m/Y H:i') }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Customer Detail Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person"></i> Data Customer</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Nama</label>
                        <p><strong>{{ $payment->invoice->booking->customer->name }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <p><strong>{{ $payment->invoice->booking->customer->email }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Nomor Telp</label>
                        <p><strong>{{ $payment->invoice->booking->customer->phone ?? '(Tidak ada)' }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Verification Checklist -->
            <div class="card mb-3 bg-light">
                <div class="card-header" style="background: #e3f2fd; border-bottom: 2px solid #0066cc;">
                    <h6 class="mb-0"><i class="bi bi-check2-circle"></i> Checklist Verifikasi</h6>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="check1">
                        <label class="form-check-label" for="check1">
                            <small>Bukti pembayaran jelas dan terlihat</small>
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="check2">
                        <label class="form-check-label" for="check2">
                            <small>Nomor referensi sesuai dengan bukti</small>
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="check3">
                        <label class="form-check-label" for="check3">
                            <small>Jumlah pembayaran sesuai invoice</small>
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check4">
                        <label class="form-check-label" for="check4">
                            <small>Penerima uang / merchant sesuai</small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                <form action="{{ route('payments.approve', $payment) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Setujui pembayaran ini?')">
                        <i class="bi bi-check-circle"></i> Setujui Pembayaran
                    </button>
                </form>
                
                <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle"></i> Tolak Pembayaran
                </button>
                
                <a href="{{ route('payments.pending') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #f8d7da; border-bottom: 2px solid #dc3545;">
                    <h5 class="modal-title">Tolak Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('payments.reject', $payment) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted">Alasan penolakan akan dikirim ke customer. Berikan penjelasan yang jelas.</p>
                        <div class="mb-0">
                            <label for="reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Contoh: Bukti pembayaran tidak jelas, nomor referensi tidak sesuai, dll..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
