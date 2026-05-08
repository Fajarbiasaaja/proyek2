<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - JASAKU | Layanan AC Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-light: #64748b;
        }

        html, body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: var(--text-dark);
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Navigation Bar */
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 16px 32px;
        }

        .navbar-brand {
            font-size: 22px;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-brand img {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px;
            border-radius: 6px;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            font-size: 13px;
            margin-left: 16px;
            transition: all 0.3s ease;
            border-radius: 6px;
            padding: 8px 14px !important;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-link-accent {
            color: #fbbf24 !important;
        }

        /* Main Login Container */
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-wrapper {
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
            position: relative;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 52px;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 20px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideInLeft 0.6s ease forwards;
        }

        .hero-section p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 32px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            animation: slideInLeft 0.6s ease 0.1s forwards;
            opacity: 0;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-top: 32px;
            animation: slideInLeft 0.6s ease 0.2s forwards;
            opacity: 0;
        }

        .feature-card {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 16px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateX(8px);
        }

        .feature-card i {
            font-size: 22px;
            color: #10b981;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .feature-card span {
            font-size: 14px;
            line-height: 1.5;
        }

        /* Illustration Container */
        .illustration-container {
            position: relative;
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: slideInRight 0.6s ease forwards;
        }

        .illustration-box {
            position: relative;
            width: 280px;
            height: 350px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .illustration-box::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translate(-100%, -100%) rotate(45deg); }
            50% { transform: translate(100%, 100%) rotate(45deg); }
        }

        .illustration-box img {
            position: relative;
            z-index: 1;
            max-width: 80%;
            max-height: 80%;
            object-fit: contain;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Login Card */
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            padding: 48px;
            height: fit-content;
            animation: slideInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            margin-bottom: 32px;
            text-align: center;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
            background: linear-gradient(135deg, #2563eb 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-header p {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            background-color: var(--light-bg);
            color: var(--text-dark);
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-control::placeholder {
            color: #cbd5e1;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
            background-color: white;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: var(--danger-color);
            margin-top: 6px;
            font-weight: 500;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--primary-dark);
        }

        .btn-sign-in {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            font-family: 'Poppins', sans-serif;
        }

        .btn-sign-in:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-sign-in:active {
            transform: translateY(0);
        }

        .btn-sign-in:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            font-size: 12px;
            color: var(--text-light);
            font-weight: 500;
        }

        .divider::before,
        .divider::after {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
            content: '';
        }

        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 12px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background-color: white;
            color: var(--text-dark);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-social i {
            font-size: 16px;
        }

        .btn-social:hover {
            background: linear-gradient(135deg, #2563eb15 0%, #764ba215 100%);
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .form-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: var(--text-light);
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Alert Styles */
        .alert {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 4px solid var(--danger-color);
            background-color: #fee2e2;
            color: #991b1b;
            font-size: 13px;
            font-weight: 500;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
            list-style: none;
        }

        .alert li {
            margin: 4px 0;
            padding-left: 20px;
            position: relative;
        }

        .alert li::before {
            content: '•';
            position: absolute;
            left: 0;
        }

        .demo-credentials {
            margin-top: 20px;
            padding: 14px;
            background: linear-gradient(135deg, #f0f9ff 0%, #f5f3ff 100%);
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            font-size: 12px;
            color: #334155;
        }

        .demo-credentials strong {
            color: var(--text-dark);
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .demo-credentials code {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Monaco', monospace;
            color: var(--primary-color);
            font-weight: 600;
        }

        .demo-row {
            margin: 6px 0;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding: 28px 32px;
            text-align: center;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .footer-links {
            margin-bottom: 12px;
            display: flex;
            justify-content: center;
            gap: 24px;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .hero-section h1 {
                font-size: 40px;
            }

            .illustration-container {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 12px 16px;
            }

            .navbar-brand {
                font-size: 20px;
            }

            .navbar-nav {
                margin-top: 12px !important;
            }

            .navbar-nav .nav-link {
                margin-left: 0;
                margin-bottom: 8px;
            }

            .login-container {
                padding: 20px;
            }

            .login-card {
                padding: 32px;
            }

            .hero-section h1 {
                font-size: 28px;
            }

            .hero-section p {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .features-grid {
                margin-top: 16px;
                gap: 12px;
            }

            .feature-card {
                padding: 12px;
                font-size: 13px;
            }

            .social-login {
                grid-template-columns: 1fr;
            }

            .footer {
                padding: 12px 16px;
            }

            .footer-links {
                gap: 12px;
                flex-wrap: wrap;
            }
        }

        /* Animation delay */
        .login-card {
            animation-delay: 0.3s;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="bi bi-snow"></i> JASAKU
            </a>
            <nav class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('login.technician') }}">
                    <i class="bi bi-tools"></i> Teknisi
                </a>
                <a class="nav-link" href="{{ route('register') }}">
                    <i class="bi bi-person-plus"></i> Daftar
                </a>
                <a class="nav-link nav-link-accent" href="{{ route('register.provider') }}">
                    <i class="bi bi-briefcase"></i> Penyedia Jasa
                </a>
            </nav>
        </div>
    </nav>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1>Udara Sejuk, Hidup Nyaman</h1>
                <p>Dapatkan layanan servis AC profesional dari teknisi berpengalaman dengan respons cepat dan harga terjangkau.</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <i class="bi bi-shield-check"></i>
                        <span>Teknisi Profesional & Tersertifikasi</span>
                    </div>
                    <div class="feature-card">
                        <i class="bi bi-clock-history"></i>
                        <span>Tersedia 24/7 untuk Kebutuhan Anda</span>
                    </div>
                    <div class="feature-card">
                        <i class="bi bi-percent"></i>
                        <span>Harga Transparan Tanpa Biaya Tersembunyi</span>
                    </div>
                    <div class="feature-card">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span>Jaminan Kepuasan dari Ribuan Pelanggan</span>
                    </div>
                </div>
            </div>

            <!-- Illustration -->
            <div class="illustration-container">
                <div class="illustration-box">
                    <i class="bi bi-snow" style="font-size: 100px; color: rgba(255, 255, 255, 0.8);"></i>
                </div>
            </div>

            <!-- Login Card -->
            <div class="login-card">
                <div class="login-header">
                    <h2><i class="bi bi-box-arrow-in-right"></i> Masuk</h2>
                    <p>Kelola pemesanan dan layanan Anda</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('auth.login') }}" method="POST" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="nama@contoh.com"
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

                    <div class="remember-forgot">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-sign-in">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider">Atau lanjutkan dengan</div>

                <!-- Social Login -->
                <div class="social-login">
                    <a href="{{ route('social.redirect', 'google') }}" class="btn-social" title="Masuk dengan Google">
                        <i class="bi bi-google"></i>
                        <span>Google</span>
                    </a>
                    <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social" title="Masuk dengan Facebook">
                        <i class="bi bi-facebook"></i>
                        <span>FB</span>
                    </a>
                    <a href="{{ route('social.redirect', 'github') }}" class="btn-social" title="Masuk dengan GitHub">
                        <i class="bi bi-github"></i>
                        <span>GitHub</span>
                    </a>
                </div>

                <!-- Footer -->
                <div class="form-footer">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a> atau <a href="{{ route('register.provider') }}">jadi penyedia jasa</a>
                </div>

                <!-- Demo Credentials -->
                <div class="demo-credentials">
                    <strong><i class="bi bi-info-circle"></i> Akun Demo Untuk Testing:</strong>
                    <div class="demo-row"><code>admin@example.com</code> | password</div>
                    <div class="demo-row"><code>customer@example.com</code> | password</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-links">
            <a href="#">Status</a>
            <a href="#">Tentang Kami</a>
            <a href="#">Blog</a>
            <a href="#">Syarat & Ketentuan</a>
            <a href="#">Privasi</a>
            <a href="#">Hubungi Kami</a>
        </div>
        <p>&copy; 2026 JASAKU - Layanan AC Profesional. Semua hak dilindungi.</p>
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
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    } else {
                        signInBtn.disabled = true;
                        signInBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Sedang Masuk...';
                    }
                });
            }
        });
    </script>
</body>
</html>
