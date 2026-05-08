@extends('layouts.app')

@section('title', 'Pembayaran Invoice')

@section('content')
    <div class="page-title">
        <i class="bi bi-credit-card"></i>
        <h1>Form Pembayaran Invoice</h1>
    </div>

    <div class="row">
        <!-- Payment Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Detail Invoice & Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Invoice Info -->
                    <div class="alert alert-info border-0 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Nomor Invoice</h6>
                                <h4 style="margin: 5px 0;">{{ $invoice->invoice_number }}</h4>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6>Jumlah Yang Harus Dibayar</h6>
                                <h4 style="margin: 5px 0; color: #fff;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-light bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted">Layanan</h6>
                                    <p class="mb-2"><strong>{{ $invoice->booking->service->name }}</strong></p>
                                    
                                    <h6 class="text-muted mt-3">Tanggal Service</h6>
                                    <p class="mb-0">{{ $invoice->booking->scheduled_date->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-light bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted">Subtotal</h6>
                                    <p class="mb-2">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</p>
                                    
                                    <h6 class="text-muted mt-3">Pajak (PPN)</h6>
                                    <p class="mb-0">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('customer.payment.submit', $invoice) }}" method="POST" enctype="multipart/form-data" onsubmit="return validatePaymentForm(event)">
                        @csrf

                        <!-- Amount -->
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Pembayaran <span class="text-danger">*</span></label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount', $invoice->total) }}" 
                                       min="0" max="{{ $invoice->total }}" step="1000" required>
                            </div>
                            <small class="text-muted">Maksimal: Rp {{ number_format($invoice->total, 0, ',', '.') }}</small>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label d-block mb-3"><strong>Metode Pembayaran <span class="text-danger">*</span></strong></label>
                            
                            <div class="mb-3">
                                <input type="hidden" name="payment_method" id="payment_method_input" 
                                       value="{{ old('payment_method', '') }}" required>
                                
                                <button type="button" class="btn btn-lg btn-outline-primary w-100" 
                                        data-bs-toggle="modal" data-bs-target="#methodModal"
                                        id="methodSelectorBtn" style="padding: 20px; border-radius: 8px;">
                                    <i class="bi bi-credit-card" style="font-size: 1.5rem;"></i><br>
                                    <span id="selectedMethodText" style="display: block; margin-top: 10px;">
                                        @if(old('payment_method'))
                                            {{ ucfirst(str_replace('_', ' ', old('payment_method'))) }}
                                        @else
                                            Pilih Metode Pembayaran
                                        @endif
                                    </span>
                                </button>
                                
                                @error('payment_method')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle"></i> Klik tombol di atas untuk memilih metode pembayaran
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- DETAIL PEMBAYARAN SESUAI METODE -->
                        <!-- ============================================ -->

                        <!-- Bank Transfer Details -->
                        <div id="bankTransferDetails" class="card mb-4" style="display: none; border-left: 4px solid #0066cc;">
                            <div class="card-header" style="background: #f0f7ff;">
                                <h6 class="mb-0"><i class="bi bi-bank"></i> <strong>Detail Bank Transfer</strong></h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Pilih salah satu rekening bank di bawah untuk melakukan transfer:</p>
                                
                                <div class="row g-3">
                                    <!-- BCA -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px;">
                                            <h6 class="mb-2"><i class="bi bi-bank" style="color: #0066cc;"></i> <strong>Bank BCA</strong></h6>
                                            <div class="mb-3 p-2 bg-light" style="border-radius: 4px;">
                                                <small class="text-muted d-block mb-1">No. Rekening:</small>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong id="bca-account">123 456 7890</strong>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyText('bca-account')">
                                                        <i class="bi bi-clipboard"></i> Copy
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block">A/N: PT JASA SERVIS AC</small>
                                        </div>
                                    </div>

                                    <!-- Mandiri -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px;">
                                            <h6 class="mb-2"><i class="bi bi-bank" style="color: #ff0000;"></i> <strong>Bank Mandiri</strong></h6>
                                            <div class="mb-3 p-2 bg-light" style="border-radius: 4px;">
                                                <small class="text-muted d-block mb-1">No. Rekening:</small>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong id="mandiri-account">987 654 3210</strong>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyText('mandiri-account')">
                                                        <i class="bi bi-clipboard"></i> Copy
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block">A/N: PT JASA SERVIS AC</small>
                                        </div>
                                    </div>

                                    <!-- BNI -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px;">
                                            <h6 class="mb-2"><i class="bi bi-bank" style="color: #00539b;"></i> <strong>Bank BNI</strong></h6>
                                            <div class="mb-3 p-2 bg-light" style="border-radius: 4px;">
                                                <small class="text-muted d-block mb-1">No. Rekening:</small>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong id="bni-account">246 813 5790</strong>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyText('bni-account')">
                                                        <i class="bi bi-clipboard"></i> Copy
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block">A/N: PT JASA SERVIS AC</small>
                                        </div>
                                    </div>

                                    <!-- Permata -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px;">
                                            <h6 class="mb-2"><i class="bi bi-bank" style="color: #0066cc;"></i> <strong>Permata Bank</strong></h6>
                                            <div class="mb-3 p-2 bg-light" style="border-radius: 4px;">
                                                <small class="text-muted d-block mb-1">No. Rekening:</small>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong id="permata-account">555 666 7777</strong>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyText('permata-account')">
                                                        <i class="bi bi-clipboard"></i> Copy
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block">A/N: PT JASA SERVIS AC</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning small mt-3 mb-0">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Penting:</strong> Transfer sesuai dengan jumlah yang tertera. Nomor referensi pembayaran akan terlihat di bukti transfer Anda.
                                </div>
                            </div>
                        </div>

                        <!-- E-Wallet Details -->
                        <div id="ewalletDetails" class="card mb-4" style="display: none; border-left: 4px solid #28a745;">
                            <div class="card-header" style="background: #f0fff4;">
                                <h6 class="mb-0"><i class="bi bi-wallet2"></i> <strong>Pembayaran E-Wallet</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="bi bi-shield-check"></i>
                                    <strong>Pembayaran Langsung via E-Wallet</strong><br>
                                    <small>Anda akan diarahkan ke payment gateway Midtrans untuk menyelesaikan pembayaran langsung dari e-wallet Anda (GoPay, OVO, Dana, LINKAJA, dll). Pembayaran akan langsung terverifikasi dan invoice akan otomatis terbayar.</small>
                                </div>

                                <div class="row g-3">
                                    <!-- GoPay -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px; background: #f0f8ff;">
                                            <h6 class="mb-2"><i class="bi bi-wallet2" style="color: #00a651;"></i> <strong>GoPay</strong></h6>
                                            <small class="text-muted d-block mb-3">Pembayaran langsung dari aplikasi GoPay</small>
                                            <p class="text-success small mb-0">✓ Instant verification</p>
                                        </div>
                                    </div>

                                    <!-- OVO -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px; background: #fff8f0;">
                                            <h6 class="mb-2"><i class="bi bi-wallet2" style="color: #9900cc;"></i> <strong>OVO</strong></h6>
                                            <small class="text-muted d-block mb-3">Pembayaran langsung dari aplikasi OVO</small>
                                            <p class="text-success small mb-0">✓ Instant verification</p>
                                        </div>
                                    </div>

                                    <!-- Dana -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px; background: #f0f7ff;">
                                            <h6 class="mb-2"><i class="bi bi-wallet2" style="color: #0066cc;"></i> <strong>Dana</strong></h6>
                                            <small class="text-muted d-block mb-3">Pembayaran langsung dari aplikasi Dana</small>
                                            <p class="text-success small mb-0">✓ Instant verification</p>
                                        </div>
                                    </div>

                                    <!-- LINKAJA -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px; background: #fffef0;">
                                            <h6 class="mb-2"><i class="bi bi-wallet2" style="color: #ff6600;"></i> <strong>LINKAJA</strong></h6>
                                            <small class="text-muted d-block mb-3">Pembayaran langsung dari aplikasi LINKAJA</small>
                                            <p class="text-success small mb-0">✓ Instant verification</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info small mt-3 mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Catatan:</strong> Klik tombol "Submit Pembayaran" untuk melanjutkan ke payment gateway dan selesaikan pembayaran dari e-wallet Anda.
                                </div>
                            </div>
                        </div>

                        <!-- Credit Card Details -->
                        <div id="creditcardDetails" class="card mb-4" style="display: none; border-left: 4px solid #6c757d;">
                            <div class="card-header" style="background: #f8f9fa;">
                                <h6 class="mb-0"><i class="bi bi-credit-card"></i> <strong>Pembayaran Kartu Kredit</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="bi bi-shield-check"></i>
                                    <strong>Pembayaran Langsung via Kartu Kredit</strong><br>
                                    <small>Anda akan diarahkan ke payment gateway Midtrans untuk menyelesaikan pembayaran menggunakan kartu kredit/debit Anda secara aman. Pembayaran akan langsung terverifikasi dan invoice akan otomatis terbayar.</small>
                                </div>
                                
                                <div class="row g-3">
                                    <!-- Visa -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px; background: #f0f5ff;">
                                            <h6 class="mb-2"><i class="bi bi-credit-card" style="color: #1434CB;"></i> <strong>Visa</strong></h6>
                                            <small class="text-muted d-block mb-3">Kartu kredit Visa</small>
                                            <p class="text-success small mb-0">✓ Instant verification</p>
                                        </div>
                                    </div>

                                    <!-- MasterCard -->
                                    <div class="col-md-6">
                                        <div class="card border" style="padding: 15px; background: #fff5f0;">
                                            <h6 class="mb-2"><i class="bi bi-credit-card" style="color: #ff5f00;"></i> <strong>Mastercard</strong></h6>
                                            <small class="text-muted d-block mb-3">Kartu kredit Mastercard</small>
                                            <p class="text-success small mb-0">✓ Instant verification</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info small mt-3 mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Catatan:</strong> Klik tombol "Submit Pembayaran" untuk melanjutkan ke payment gateway dan selesaikan pembayaran dengan kartu kredit/debit Anda.
                                </div>
                            </div>
                        </div>

                        <!-- Cash Details -->
                        <div id="cashDetails" class="card mb-4" style="display: none; border-left: 4px solid #28a745;">
                            <div class="card-header" style="background: #f0fff4;">
                                <h6 class="mb-0"><i class="bi bi-cash-coin"></i> <strong>Pembayaran Tunai</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success small mb-0">
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Bayar langsung saat technician datang untuk melakukan service.</strong><br>
                                    Silakan siapkan uang tunai sesuai dengan jumlah yang tertera: <strong>Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Reference Number -->
                        <div class="mb-3" id="referenceField" style="display: none;">
                            <label for="reference_number" class="form-label">Nomor Referensi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                   id="reference_number" name="reference_number" value="{{ old('reference_number') }}" 
                                   placeholder="Nomor referensi / nomor transfer">
                            <small class="text-muted d-block mt-2" id="referenceHint">
                                <strong>Contoh:</strong> Nomor referensi transfer dari bank atau e-wallet Anda
                            </small>
                            @error('reference_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Proof / Screenshot -->
                        <div class="mb-4" id="proofField" style="display: none;">
                            <label for="payment_proof" class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" 
                                       id="payment_proof" name="payment_proof" accept="image/jpeg,image/jpg,image/png,application/pdf" 
                                       onchange="previewProof()" required>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Format: JPG, PNG, PDF | Ukuran max: 50MB | Screenshot bukti transfer / struk pembayaran
                            </small>
                            @error('payment_proof')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="mt-3">
                                <img id="proofPreview" src="" alt="Preview Bukti" style="max-width: 100%; max-height: 200px; border-radius: 4px; display: none;">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Catatan tambahan untuk admin...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('customer.invoice.show', $invoice) }}" class="btn btn-light">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Submit Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Status Invoice</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Tanggal Invoice</small>
                        <p class="mb-2">{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Batas Pembayaran</small>
                        <p class="mb-2">{{ $invoice->due_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <small class="text-muted">Status</small>
                        <p>
                            <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bank Accounts Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bank"></i> Rekening Bank</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1"><strong>BCA</strong></small>
                        <small>123 456 7890</small><br>
                        <small class="text-muted">PT JASA SERVIS AC</small>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1"><strong>Mandiri</strong></small>
                        <small>987 654 3210</small><br>
                        <small class="text-muted">PT JASA SERVIS AC</small>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1"><strong>BNI</strong></small>
                        <small>246 813 5790</small><br>
                        <small class="text-muted">PT JASA SERVIS AC</small>
                    </div>
                </div>
            </div>

            <!-- E-Wallet Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-wallet2"></i> E-Wallet</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1"><strong>Dana</strong></small>
                        <small>0812-3456-7890</small>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1"><strong>OVO</strong></small>
                        <small>0812-3456-7890</small>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1"><strong>GCash</strong></small>
                        <small>+63 912 345 6789</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-question-circle"></i> Bantuan</h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">Bagaimana cara pembayaran?</h6>
                    <ol class="small" style="padding-left: 20px;">
                        <li>Pilih metode pembayaran</li>
                        <li>Transfer ke rekening/e-wallet yang ditampilkan</li>
                        <li>Masukkan nomor referensi transfer</li>
                        <li>Upload bukti pembayaran (screenshot)</li>
                        <li>Tunggu verifikasi admin (max 1x24 jam)</li>
                    </ol>
                    <div class="alert alert-success small mt-3 mb-0">
                        <i class="bi bi-check-circle"></i>
                        <strong>Tunai:</strong> Bayar langsung ke technician saat service
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validatePaymentForm(event) {
            try {
                const method = document.getElementById('payment_method_input').value;
                const proofInput = document.getElementById('payment_proof');
                const referenceInput = document.getElementById('reference_number');
                const proofField = document.getElementById('proofField');
                const referenceField = document.getElementById('referenceField');

                // Validate payment method selected
                if (!method) {
                    if (event && event.preventDefault) {
                        event.preventDefault();
                        alert('Pilih metode pembayaran terlebih dahulu');
                    }
                    return false;
                }

                // For gateway methods (e-wallet, credit_card), no manual validation needed
                if (['e_wallet', 'credit_card'].includes(method)) {
                    // Gateway methods don't require proof or reference - handled by Midtrans
                    return true;
                }

                // Validate file is selected if proof is required
                if (proofField && proofField.style.display !== 'none') {
                    if (!proofInput || !proofInput.files || proofInput.files.length === 0) {
                        if (event && event.preventDefault) {
                            event.preventDefault();
                            alert('Mohon unggah bukti pembayaran');
                        }
                        return false;
                    }
                }

                // Validate reference number if required
                if (referenceField && referenceField.style.display !== 'none') {
                    if (!referenceInput || !referenceInput.value.trim()) {
                        if (event && event.preventDefault) {
                            event.preventDefault();
                            alert('Mohon masukkan nomor referensi pembayaran');
                        }
                        return false;
                    }
                }

                return true;
            } catch (error) {
                console.error('Validation error:', error);
                return true; // Allow submission if validation fails
            }
        }

        function copyText(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                // Show success message
                const originalText = element.textContent;
                element.textContent = '✓ Tersalin!';
                element.style.color = 'green';
                
                setTimeout(() => {
                    element.textContent = originalText;
                    element.style.color = '';
                }, 2000);
            });
        }

        function updateMethodFields() {
            try {
                const method = document.getElementById('payment_method_input').value;
                const referenceField = document.getElementById('referenceField');
                const proofField = document.getElementById('proofField');
                const proofInput = document.getElementById('payment_proof');
                const referenceHint = document.getElementById('referenceHint');
                
                // Hide all detail sections
                const detailSections = ['bankTransferDetails', 'ewalletDetails', 'creditcardDetails', 'cashDetails'];
                detailSections.forEach(sectionId => {
                    const section = document.getElementById(sectionId);
                    if (section) section.style.display = 'none';
                });

                if (!method) {
                    if (referenceField) referenceField.style.display = 'none';
                    if (proofField) proofField.style.display = 'none';
                    if (proofInput) proofInput.removeAttribute('required');
                } else if (method === 'cash') {
                    if (referenceField) referenceField.style.display = 'none';
                    if (proofField) proofField.style.display = 'block';
                    if (proofInput) proofInput.setAttribute('required', 'required');
                    const cashDetails = document.getElementById('cashDetails');
                    if (cashDetails) cashDetails.style.display = 'block';
                } else if (method === 'bank_transfer') {
                    if (referenceField) referenceField.style.display = 'block';
                    if (proofField) proofField.style.display = 'block';
                    if (proofInput) proofInput.setAttribute('required', 'required');
                    if (referenceHint) {
                        referenceHint.innerHTML = '<strong>Contoh:</strong> Masukkan nomor referensi transfer dari bank Anda (ada di bukti transfer)';
                    }
                    const bankTransferDetails = document.getElementById('bankTransferDetails');
                    if (bankTransferDetails) bankTransferDetails.style.display = 'block';
                } else if (method === 'check') {
                    if (referenceField) referenceField.style.display = 'block';
                    if (proofField) proofField.style.display = 'block';
                    if (proofInput) proofInput.setAttribute('required', 'required');
                    if (referenceHint) {
                        referenceHint.innerHTML = '<strong>Contoh:</strong> Masukkan nomor cek Anda';
                    }
                } else if (method === 'e_wallet') {
                    // E-Wallet dengan Midtrans gateway - HIDE manual fields
                    if (referenceField) referenceField.style.display = 'none';
                    if (proofField) proofField.style.display = 'none';
                    if (proofInput) proofInput.removeAttribute('required');
                    const ewalletDetails = document.getElementById('ewalletDetails');
                    if (ewalletDetails) ewalletDetails.style.display = 'block';
                } else if (method === 'credit_card') {
                    // Credit Card dengan Midtrans gateway - HIDE manual fields
                    if (referenceField) referenceField.style.display = 'none';
                    if (proofField) proofField.style.display = 'none';
                    if (proofInput) proofInput.removeAttribute('required');
                    const creditcardDetails = document.getElementById('creditcardDetails');
                    if (creditcardDetails) creditcardDetails.style.display = 'block';
                }
            } catch (error) {
                console.error('Error updating method fields:', error);
            }
        }

        function selectPaymentMethod(methodValue, methodName) {
            document.getElementById('payment_method_input').value = methodValue;
            document.getElementById('selectedMethodText').textContent = methodName;
            
            // Close modal dengan berbagai method untuk memastikan tertutup
            try {
                // Method 1: Gunakan Bootstrap modal instance
                const modalElement = document.getElementById('methodModal');
                if (modalElement && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    } else {
                        // Jika instance tidak ada, buat baru
                        new bootstrap.Modal(modalElement).hide();
                    }
                }
                
                // Method 2: Direct DOM manipulation sebagai fallback
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                document.body.classList.remove('modal-open');
                
            } catch (error) {
                console.error('Error closing modal:', error);
                // Jika ada error, tetap lanjutkan proses
            }
            
            // Update method fields dan trigger validation
            updateMethodFields();
            validatePaymentForm();
            
            // Scroll to detail section setelah delay untuk memastikan modal sudah tertutup
            setTimeout(() => {
                const detailSections = [
                    document.getElementById('bankTransferDetails'),
                    document.getElementById('ewalletDetails'),
                    document.getElementById('creditcardDetails'),
                    document.getElementById('cashDetails')
                ];
                
                for (let section of detailSections) {
                    if (section && section.offsetParent !== null) {
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        break;
                    }
                }
            }, 500);
        }

        function previewProof() {
            const input = document.getElementById('payment_proof');
            const preview = document.getElementById('proofPreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize
        updateMethodFields();
    </script>

    <!-- ============================================ -->
    <!-- MODAL: PILIH METODE PEMBAYARAN -->
    <!-- ============================================ -->
    <div class="modal fade" id="methodModal" tabindex="-1" aria-labelledby="methodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white;">
                    <h5 class="modal-title" id="methodModalLabel">
                        <i class="bi bi-credit-card"></i> Pilih Metode Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-3">
                    <!-- Bank Transfer Section -->
                    <div class="mb-4">
                        <h6 style="color: #0066cc; margin-bottom: 15px;">
                            <i class="bi bi-bank"></i> <strong>Transfer Bank</strong>
                        </h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary text-start" 
                                    onclick="selectPaymentMethod('bank_transfer', 'Bank Transfer')">
                                <i class="bi bi-bank" style="color: #0066cc;"></i> <strong>Bank Transfer</strong>
                                <br><small class="text-muted">BCA, Mandiri, BNI, Permata</small>
                            </button>
                        </div>
                    </div>

                    <hr>

                    <!-- E-Wallet Section -->
                    <div class="mb-4">
                        <h6 style="color: #00cc66; margin-bottom: 15px;">
                            <i class="bi bi-wallet2"></i> <strong>E-Wallet</strong>
                        </h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success text-start"
                                    onclick="selectPaymentMethod('e_wallet', 'E-Wallet (Dana, OVO, GCash)')">
                                <i class="bi bi-wallet2"></i> <strong>E-Wallet</strong>
                                <br><small class="text-muted">Dana, OVO, GCash</small>
                            </button>
                        </div>
                    </div>

                    <hr>

                    <!-- Credit Card Section -->
                    <div class="mb-4">
                        <h6 style="color: #6c757d; margin-bottom: 15px;">
                            <i class="bi bi-credit-card"></i> <strong>Kartu Kredit</strong>
                        </h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary text-start"
                                    onclick="selectPaymentMethod('credit_card', 'Kartu Kredit')">
                                <i class="bi bi-credit-card"></i> <strong>Kartu Kredit</strong>
                                <br><small class="text-muted">Visa / Mastercard</small>
                            </button>
                        </div>
                    </div>

                    <hr>

                    <!-- Cash Section -->
                    <div class="mb-2">
                        <h6 style="color: #28a745; margin-bottom: 15px;">
                            <i class="bi bi-cash-coin"></i> <strong>Tunai</strong>
                        </h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success text-start"
                                    onclick="selectPaymentMethod('cash', 'Tunai (Bayar saat service)')">
                                <i class="bi bi-cash-coin"></i> <strong>Tunai</strong>
                                <br><small class="text-muted">Bayar langsung saat technician datang</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
