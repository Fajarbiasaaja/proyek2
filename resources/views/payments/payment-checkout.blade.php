@extends('layouts.app')

@section('title', 'Checkout Pembayaran')

@section('content')
<style>
    .checkout-container {
        background: #f5f5f5;
        padding: 20px 0;
        min-height: calc(100vh - 80px);
    }
    
    .checkout-header {
        background: white;
        padding: 20px;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 20px;
    }
    
    .checkout-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 20px;
    }
    
    @media (max-width: 768px) {
        .checkout-layout {
            grid-template-columns: 1fr;
        }
        .order-summary {
            order: 2;
        }
    }
    
    .checkout-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #f0f0f0;
    }
    
    .checkout-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ee4d2d;
        color: #222;
    }
    
    .payment-method-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .payment-method-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .payment-option {
        border: 2px solid #f0f0f0;
        border-radius: 8px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        user-select: none;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }
    
    .payment-option:hover {
        border-color: #ee4d2d;
        background: #fff9f7;
    }
    
    .payment-option.active {
        border-color: #ee4d2d;
        background: #fff9f7;
        box-shadow: 0 0 0 4px rgba(238, 77, 45, 0.1);
    }
    
    .payment-option input[type="radio"] {
        margin: 0;
        cursor: pointer;
        width: 20px;
        height: 20px;
        accent-color: #ee4d2d;
        flex-shrink: 0;
    }
    
    .payment-method-content {
        flex: 1;
    }
    
    .payment-icon {
        font-size: 24px;
        margin-bottom: 8px;
        display: block;
    }
    
    .payment-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    .payment-desc {
        font-size: 12px;
        color: #999;
    }
    
    .order-summary {
        position: sticky;
        top: 100px;
    }
    
    .summary-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #f0f0f0;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .summary-item.total {
        border-bottom: 2px solid #ee4d2d;
        font-weight: 600;
        font-size: 16px;
        color: #ee4d2d;
        margin-top: 12px;
        padding-top: 12px;
        padding-bottom: 12px;
    }
    
    .summary-label {
        color: #666;
        font-size: 13px;
    }
    
    .summary-value {
        font-weight: 500;
        color: #222;
    }
    
    .invoice-preview {
        background: #f9f9f9;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .invoice-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 13px;
    }
    
    .invoice-row-label {
        color: #666;
    }
    
    .invoice-row-value {
        font-weight: 500;
    }
    
    .buyer-protection {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border-left: 4px solid #4caf50;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .buyer-protection-title {
        font-weight: 600;
        color: #2e7d32;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .buyer-protection-text {
        font-size: 12px;
        color: #558b2f;
        line-height: 1.4;
    }
    
    .submit-button {
        width: 100%;
        padding: 14px;
        background: #ee4d2d;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .submit-button:hover {
        background: #d63821;
    }
    
    .submit-button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    
    .payment-info-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #1976d2;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .payment-info-title {
        font-weight: 600;
        color: #1565c0;
        margin-bottom: 8px;
        font-size: 13px;
    }
    
    .payment-info-text {
        font-size: 12px;
        color: #0d47a1;
        line-height: 1.4;
    }
    
    .bank-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .bank-item {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .bank-item:hover {
        border-color: #ee4d2d;
        background: #fff9f7;
    }
    
    .bank-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    .bank-account {
        font-family: monospace;
        font-size: 12px;
        color: #666;
        margin-bottom: 6px;
        word-break: break-all;
    }
    
    .copy-btn {
        font-size: 11px;
        padding: 4px 8px;
        background: #ee4d2d;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .copy-btn:hover {
        background: #d63821;
    }
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <div class="container-lg">
            <h2 style="margin: 0; font-size: 24px; font-weight: 600;">
                <i class="bi bi-cart-check"></i> Checkout
            </h2>
        </div>
    </div>

    <div class="container-lg">
        <div class="checkout-layout">
            <!-- Main Content -->
            <div class="checkout-main">
                <!-- Step 1: Review Order -->
                <div class="checkout-section">
                    <div class="checkout-title">
                        <i class="bi bi-receipt"></i> 1. Periksa Pesanan
                    </div>

                    <div class="invoice-preview">
                        <div class="invoice-row">
                            <div class="invoice-row-label"><strong>Nomor Invoice</strong></div>
                            <div class="invoice-row-value">{{ $invoice->invoice_number }}</div>
                        </div>
                        <div class="invoice-row">
                            <div class="invoice-row-label">Layanan</div>
                            <div class="invoice-row-value">{{ $invoice->booking->service->name }}</div>
                        </div>
                        <div class="invoice-row">
                            <div class="invoice-row-label">Tanggal Layanan</div>
                            <div class="invoice-row-value">{{ $invoice->booking->scheduled_date->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="invoice-row">
                            <div class="invoice-row-label">Status</div>
                            <div class="invoice-row-value">
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success">Sudah Dibayar</span>
                                @elseif($invoice->status === 'issued')
                                    <span class="badge bg-warning">Belum Dibayar</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($paidAmount > 0)
                        <div class="alert alert-info" style="margin-bottom: 20px;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Informasi:</strong> Anda sudah membayar Rp {{ number_format($paidAmount, 0, ',', '.') }} sebelumnya. 
                            Sisa pembayaran: Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                        </div>
                    @endif
                </div>

                <!-- Step 2: Select Payment Method -->
                <div class="checkout-section">
                    <div class="checkout-title">
                        <i class="bi bi-credit-card"></i> 2. Pilih Metode Pembayaran
                    </div>

                    <form id="checkoutForm" action="{{ route('customer.payment.submit', $invoice) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Payment Method Options -->
                        <div class="payment-method-grid">
                            <!-- E-Wallet -->
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="e_wallet" required onchange="selectPaymentMethod('e_wallet')">
                                <div class="payment-method-content">
                                    <span class="payment-icon">💳</span>
                                    <div class="payment-name">E-Wallet</div>
                                    <div class="payment-desc">GoPay, OVO, Dana, LINKAJA</div>
                                </div>
                            </label>

                            <!-- Credit Card -->
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="credit_card" required onchange="selectPaymentMethod('credit_card')">
                                <div class="payment-method-content">
                                    <span class="payment-icon">💰</span>
                                    <div class="payment-name">Kartu Kredit</div>
                                    <div class="payment-desc">Visa, Mastercard</div>
                                </div>
                            </label>

                            <!-- Bank Transfer -->
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank_transfer" required onchange="selectPaymentMethod('bank_transfer')">
                                <div class="payment-method-content">
                                    <span class="payment-icon">🏦</span>
                                    <div class="payment-name">Transfer Bank</div>
                                    <div class="payment-desc">BCA, Mandiri, BNI, Permata</div>
                                </div>
                            </label>

                            <!-- Cash -->
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash" required onchange="selectPaymentMethod('cash')">
                                <div class="payment-method-content">
                                    <span class="payment-icon">💵</span>
                                    <div class="payment-name">Tunai</div>
                                    <div class="payment-desc">Pembayaran Langsung</div>
                                </div>
                            </label>
                        </div>

                        <!-- Amount Input -->
                        <div class="mb-4">
                            <label for="amount" class="form-label" style="font-weight: 600;">Jumlah Pembayaran *</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #f5f5f5; border: 1px solid #e0e0e0;">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" 
                                       value="{{ old('amount', $remainingAmount) }}" 
                                       min="1" max="{{ $remainingAmount }}" 
                                       step="1000" required
                                       style="border: 1px solid #e0e0e0; font-weight: 600; font-size: 16px;">
                            </div>
                            @error('amount')
                                <div class="alert alert-danger mt-2" style="font-size: 12px; padding: 8px 12px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamic Payment Details -->
                        <div id="paymentDetails"></div>

                        <input type="hidden" id="reference_number" name="reference_number" value="">
                        <input type="hidden" id="payment_proof" name="payment_proof" value="">

                        <!-- Buyer Protection Info -->
                        <div class="buyer-protection">
                            <div class="buyer-protection-title">
                                <i class="bi bi-shield-check"></i> Perlindungan Pembeli
                            </div>
                            <div class="buyer-protection-text">
                                Pembayaran Anda akan dilindungi selama 30 hari. Jika ada masalah dengan transaksi, Anda dapat mengajukan klaim.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="submit-button" id="submitBtn" disabled>
                            <i class="bi bi-check-circle"></i> Lanjutkan Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary (Sticky) -->
            <div class="order-summary">
                <div class="summary-card">
                    <div style="font-weight: 600; margin-bottom: 15px; font-size: 14px;">RINGKASAN PESANAN</div>

                    <div class="summary-item">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Pajak (PPN)</span>
                        <span class="summary-value">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                    </div>

                    @if($paidAmount > 0)
                        <div class="summary-item">
                            <span class="summary-label">Sudah Dibayar</span>
                            <span class="summary-value" style="color: #4caf50;">- Rp {{ number_format($paidAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="summary-item total">
                        <span>Total Bayar</span>
                        <span id="totalDisplay">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                    </div>

                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f5f5f5;">
                        <div style="background: #f0f7ff; padding: 10px; border-radius: 4px; font-size: 12px; color: #0d47a1;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Info:</strong> Pembayaran Anda akan diverifikasi dalam 1-2 jam kerja
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple and direct approach
    console.log('Payment checkout script loaded');
    
    const submitBtn = document.getElementById('submitBtn');
    const amountInput = document.getElementById('amount');
    const paymentDetails = document.getElementById('paymentDetails');
    const maxAmount = {{ $remainingAmount }};

    // Function to select payment method
    window.selectPaymentMethod = function(method) {
        console.log('selectPaymentMethod called:', method);
        
        // Find and check the radio button
        const radio = document.querySelector(`input[name="payment_method"][value="${method}"]`);
        if (radio) {
            radio.checked = true;
            console.log('Radio checked:', radio.checked);
            
            // Update visual state
            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.remove('active');
            });
            radio.closest('.payment-option')?.classList.add('active');
            
            // Render details
            renderPaymentDetails(method);
            
            // Enable submit if amount is set
            checkFormValidity();
        }
    };

    // Function to check form validity
    window.checkFormValidity = function() {
        const methodSelected = document.querySelector('input[name="payment_method"]:checked') !== null;
        const amountValid = amountInput.value > 0;
        
        console.log('Form validity - method:', methodSelected, 'amount:', amountValid);
        
        if (submitBtn) {
            submitBtn.disabled = !(methodSelected && amountValid);
        }
    };

    // Update amount display
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            let amount = parseInt(this.value) || 0;
            if (amount > maxAmount) {
                amount = maxAmount;
                this.value = amount;
            }
            const totalDisplay = document.getElementById('totalDisplay');
            if (totalDisplay) {
                totalDisplay.textContent = 'Rp ' + amount.toLocaleString('id-ID');
            }
            checkFormValidity();
        });
    }

    // Add change listener to all radio buttons
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('Radio changed:', this.value);
            window.selectPaymentMethod(this.value);
        });
    });

    window.renderPaymentDetails = function(method) {
        if (!paymentDetails) return;
        
        paymentDetails.innerHTML = '';

        if (method === 'bank_transfer') {
            paymentDetails.innerHTML = `
                <div class="payment-info-box">
                    <div class="payment-info-title">
                        <i class="bi bi-info-circle"></i> Instruksi Transfer Bank
                    </div>
                    <div class="payment-info-text">
                        Silakan transfer ke salah satu rekening berikut. Pergunakan nomor invoice sebagai referensi transfer.
                    </div>
                </div>
                <div class="bank-list">
                    <div class="bank-item">
                        <div class="bank-name">🏦 BCA</div>
                        <div class="bank-account">123 456 7890</div>
                        <button type="button" class="copy-btn" onclick="copyToClipboard('123 456 7890')">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <div class="bank-item">
                        <div class="bank-name">🏦 Mandiri</div>
                        <div class="bank-account">987 654 3210</div>
                        <button type="button" class="copy-btn" onclick="copyToClipboard('987 654 3210')">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <div class="bank-item">
                        <div class="bank-name">🏦 BNI</div>
                        <div class="bank-account">246 813 5790</div>
                        <button type="button" class="copy-btn" onclick="copyToClipboard('246 813 5790')">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <div class="bank-item">
                        <div class="bank-name">🏦 Permata</div>
                        <div class="bank-account">555 666 7777</div>
                        <button type="button" class="copy-btn" onclick="copyToClipboard('555 666 7777')">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>
            `;
        } else if (method === 'e_wallet') {
            paymentDetails.innerHTML = `
                <div class="payment-info-box">
                    <div class="payment-info-title">
                        <i class="bi bi-info-circle"></i> Pembayaran E-Wallet
                    </div>
                    <div class="payment-info-text">
                        Anda akan diarahkan ke Midtrans untuk menyelesaikan pembayaran. Pilih e-wallet favorit Anda (GoPay, OVO, Dana, LINKAJA).
                    </div>
                </div>
            `;
        } else if (method === 'credit_card') {
            paymentDetails.innerHTML = `
                <div class="payment-info-box">
                    <div class="payment-info-title">
                        <i class="bi bi-info-circle"></i> Pembayaran Kartu Kredit
                    </div>
                    <div class="payment-info-text">
                        Anda akan diarahkan ke Midtrans untuk memasukkan data kartu kredit secara aman. Pembayaran akan diproses langsung dan otomatis terverifikasi.
                    </div>
                </div>
            `;
        } else if (method === 'cash') {
            paymentDetails.innerHTML = `
                <div class="payment-info-box">
                    <div class="payment-info-title">
                        <i class="bi bi-info-circle"></i> Pembayaran Tunai
                    </div>
                    <div class="payment-info-text">
                        Silakan transfer bukti pembayaran (foto struk) untuk verifikasi. Pembayaran akan diverifikasi dalam 1-2 jam kerja.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="payment_proof" class="form-label">Upload Bukti Pembayaran *</label>
                    <input type="file" class="form-control" id="payment_proof" name="payment_proof" 
                           accept="image/*,.pdf" required>
                    <small class="text-muted">Format: JPG, PNG, atau PDF (Max 5MB)</small>
                </div>
            `;
        }
    }

    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Nomor rekening berhasil disalin!');
        });
    };

    console.log('Payment checkout script ready');
</script>
@endsection
