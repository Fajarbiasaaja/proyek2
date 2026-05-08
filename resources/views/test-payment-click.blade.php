<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment Click</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .payment-method-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            display: flex;
            align-items: center;
            gap: 15px;
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
            font-size: 28px;
            display: block;
            margin-bottom: 4px;
        }
        
        .payment-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
            color: #222;
        }
        
        .payment-desc {
            font-size: 12px;
            color: #999;
        }
        
        .result {
            background: #f0f7ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .result h4 {
            margin-top: 0;
            color: #0066cc;
        }
        
        .result p {
            margin: 5px 0;
            font-size: 14px;
            color: #333;
        }
        
        button {
            background: #ee4d2d;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        button:hover {
            background: #d63821;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧪 Test Payment Method Selection</h2>
        
        <form id="testForm">
            <div class="payment-method-grid">
                <!-- E-Wallet -->
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="e_wallet" onchange="handleSelect('e_wallet')">
                    <div class="payment-method-content">
                        <span class="payment-icon">💳</span>
                        <div class="payment-name">E-Wallet</div>
                        <div class="payment-desc">GoPay, OVO, Dana, LINKAJA</div>
                    </div>
                </label>

                <!-- Credit Card -->
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="credit_card" onchange="handleSelect('credit_card')">
                    <div class="payment-method-content">
                        <span class="payment-icon">💰</span>
                        <div class="payment-name">Kartu Kredit</div>
                        <div class="payment-desc">Visa, Mastercard</div>
                    </div>
                </label>

                <!-- Bank Transfer -->
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="bank_transfer" onchange="handleSelect('bank_transfer')">
                    <div class="payment-method-content">
                        <span class="payment-icon">🏦</span>
                        <div class="payment-name">Transfer Bank</div>
                        <div class="payment-desc">BCA, Mandiri, BNI, Permata</div>
                    </div>
                </label>

                <!-- Cash -->
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cash" onchange="handleSelect('cash')">
                    <div class="payment-method-content">
                        <span class="payment-icon">💵</span>
                        <div class="payment-name">Tunai</div>
                        <div class="payment-desc">Pembayaran Langsung</div>
                    </div>
                </label>
            </div>
            
            <button type="button" onclick="submitForm()">Submit</button>
        </form>
        
        <div class="result" id="result" style="display: none;">
            <h4>✅ Hasil Seleksi</h4>
            <p><strong>Metode Pembayaran:</strong> <span id="selectedMethod">-</span></p>
            <p><strong>Status:</strong> <span id="status">Siap disubmit</span></p>
        </div>
    </div>

    <script>
        function handleSelect(method) {
            console.log('handleSelect called:', method);
            
            // Update active state
            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.remove('active');
            });
            
            const radio = document.querySelector(`input[value="${method}"]`);
            if (radio && radio.closest('.payment-option')) {
                radio.closest('.payment-option').classList.add('active');
            }
            
            // Show result
            const result = document.getElementById('result');
            result.style.display = 'block';
            document.getElementById('selectedMethod').textContent = method;
        }
        
        function submitForm() {
            const selected = document.querySelector('input[name="payment_method"]:checked');
            if (selected) {
                alert('✅ Selected: ' + selected.value);
            } else {
                alert('❌ Please select a payment method');
            }
        }
        
        // Debug: Log all click events
        document.querySelectorAll('.payment-option').forEach(el => {
            el.addEventListener('click', function(e) {
                console.log('Label clicked:', e.target);
            });
        });
    </script>
</body>
</html>
