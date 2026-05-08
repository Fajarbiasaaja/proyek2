<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Teknisi - JASAKU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2a 0%, #26334a 50%, #2d3b52 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #24292f;
        }

        /* Navigation Bar */
        .navbar {
            background-color: rgba(26, 26, 42, 0.95);
            border-bottom: 2px solid #ff6b35;
            padding: 12px 24px;
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-size: 20px;
            font-weight: 700;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-brand img {
            width: 28px;
            height: 28px;
        }

        .navbar-badge {
            display: inline-block;
            background-color: #ff6b35;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .navbar a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .navbar a:hover {
            color: #ff6b35;
        }

        /* Main Container */
        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-content {
            width: 100%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        /* Hero Section */
        .hero-section {
            color: white;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 48px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 16px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .hero-section h1 .highlight {
            color: #ff6b35;
            display: block;
            margin-top: 8px;
        }

        .hero-section p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            margin-bottom: 32px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 32px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .feature-item i {
            font-size: 20px;
            color: #ff6b35;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .hero-image {
            position: relative;
            height: 400px;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.15) 0%, rgba(100, 200, 255, 0.15) 100%);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 107, 53, 0.3);
        }

        .hero-image i {
            font-size: 120px;
            color: rgba(255, 107, 53, 0.3);
        }

        .hero-image img {
            width: 480px;
            max-width: 95%;
            height: auto;
            object-fit: contain;
            opacity: 1;
            display: block;
        }

        /* Form Card */
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            padding: 40px;
            height: fit-content;
            border-top: 4px solid #ff6b35;
        }

        .login-header {
            margin-bottom: 28px;
            text-align: center;
        }

        .login-header .tech-icon {
            font-size: 40px;
            color: #ff6b35;
            margin-bottom: 12px;
        }

        .login-header h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a2a;
        }

        .login-header p {
            font-size: 14px;
            color: #57606a;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #24292f;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d0d7de;
            border-radius: 6px;
            font-size: 14px;
            background-color: #ffffff;
            color: #24292f;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-control::placeholder {
            color: #9199a1;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-control.is-invalid {
            border-color: #da3633;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(218, 54, 51, 0.1);
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #da3633;
            margin-top: 4px;
        }

        .btn-sign-in {
            width: 100%;
            padding: 10px 16px;
            margin-top: 20px;
            background-color: #ff6b35;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-sign-in:hover {
            background-color: #ff5520;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(255, 107, 53, 0.3);
        }

        .btn-sign-in:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            font-size: 12px;
            color: #57606a;
        }

        .divider::before,
        .divider::after {
            flex: 1;
            height: 1px;
            background-color: #d0d7de;
            content: '';
        }

        .form-footer {
            margin-top: 16px;
            text-align: center;
            font-size: 12px;
            color: #57606a;
        }

        .form-footer a {
            color: #ff6b35;
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .demo-box {
            margin-top: 20px;
            padding: 12px;
            background-color: #fff8f5;
            border: 1px solid #ff6b35;
            border-radius: 6px;
            font-size: 11px;
            color: #5a4a42;
        }

        .demo-box strong {
            color: #d9480f;
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .demo-box p {
            margin: 2px 0;
            line-height: 1.4;
        }

        .alert {
            margin-bottom: 16px;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #d1394f;
            background-color: #ffebe6;
            color: #82071e;
            font-size: 12px;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert li {
            margin: 3px 0;
        }

        /* Back to Login Button */
        .back-to-login {
            display: inline-block;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .back-to-login a {
            color: #ff6b35;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .back-to-login a:hover {
            gap: 10px;
        }

        /* Footer */
        .footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin: 0 8px;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: #ff6b35;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .hero-section h1 {
                font-size: 32px;
            }

            .hero-section p {
                font-size: 14px;
            }

            .hero-image {
                height: 300px;
                display: none;
            }

            .hero-features {
                display: none;
            }

            .login-card {
                padding: 30px;
            }

            .navbar {
                padding: 12px 16px;
            }

            .login-wrapper {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('img/jasaku-logo.svg') }}" alt="JASAKU">
                JASAKU
                <span class="navbar-badge">Teknisi</span>
            </a>
            <div class="ms-auto">
                <a href="/">Kembali ke Login</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="login-wrapper">
        <div class="login-content">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1>
                    Portal
                    <span class="highlight">Teknisi</span>
                </h1>
                <p>Kelola pekerjaan Anda, pantau booking aktif, dan kelola jadwal servis dengan mudah melalui dashboard teknisi kami.</p>

                <div class="hero-features">
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Kelola jadwal kerja harian Anda</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Pantau status booking dan pekerjaan</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Lihat detail pelanggan dan lokasi servis</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Kelola riwayat dan rating kerja Anda</span>
                    </div>
                </div>

                <div class="hero-image">
                    <i class="bi bi-tools"></i>
                </div>
            </div>

            <!-- Login Card -->
            <div class="login-card">
                <div class="login-header">
                    <div class="tech-icon">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <h2>Masuk Akun Teknisi</h2>
                    <p>Akses dashboard kerja Anda</p>
                </div>

                <div class="back-to-login">
                    <a href="/">
                        <i class="bi bi-arrow-left"></i> Kembali ke Login Umum
                    </a>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('technician.login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email Teknisi</label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="email@contoh.com"
                            required 
                            autofocus
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-sign-in">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dashboard
                    </button>
                </form>

                <!-- Footer Info -->
                <div class="form-footer">
                    <p>Butuh bantuan? 
                        <a href="mailto:support@jasaku.com">Hubungi support</a>
                    </p>
                </div>

                <!-- Demo Credentials -->
                <div class="demo-box">
                    <strong><i class="bi bi-info-circle"></i> Akun Demo Teknisi:</strong>
                    <p><strong>Email:</strong> hendra@example.com</p>
                    <p><strong>Password:</strong> password</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="#">Status</a>
        <a href="#">Tentang</a>
        <a href="#">Blog</a>
        <a href="#">Syarat</a>
        <a href="#">Privasi</a>
        <a href="#">Bantuan</a>
        <p style="margin: 12px 0 0 0;">&copy; 2026 JASAKU. Semua hak dilindungi.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('.form-control');
            const signInBtn = document.querySelector('.btn-sign-in');

            // Remove invalid class when typing
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                if (form.checkValidity() === false) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    signInBtn.disabled = true;
                    signInBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sedang masuk...';
                }
                form.classList.add('was-validated');
            });
        });
    </script>
</body>
</html>
