<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - JASAKU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Reset */
        *{box-sizing:border-box;margin:0;padding:0}

        /* Theme variables */
        :root{
            --primary-color:#0969da;
            --primary-dark:#0860ca;
            --success-color:#238636;
            --danger-color:#da3633;
            --warning-color:#d29922;
            --border-color:rgba(255,255,255,0.06);
            /* Dark defaults */
            --text-primary:#ffffff;
            --text-secondary:rgba(255,255,255,0.8);
            --bg-light:rgba(255,255,255,0.03);
            --bg-dark:#0d1b2a;
            --sidebar-bg:rgba(13,27,42,0.95);
            --navbar-bg:rgba(13,27,42,0.95);
            --page-bg:linear-gradient(135deg,var(--bg-dark) 0%,#1a235e 50%,#16213e 100%);
            --card-bg:rgba(255,255,255,0.03);
            --surface-contrast:rgba(255,255,255,0.04);
            --nav-link-rgb:255,255,255;
        }

        html,body{height:100%}

        /* Light theme overrides */
        body.theme-light{
            --border-color:#e6eef8;
            --text-primary:#12263a;
            --text-secondary:#57606a;
            --bg-light:#f6f8fa;
            --bg-dark:#ffffff;
            --sidebar-bg:rgba(255,255,255,1);
            --navbar-bg:rgba(255,255,255,1);
            --page-bg:#ffffff;
            --card-bg:#ffffff;
            --surface-contrast:#f6f8fa;
            --nav-link-rgb:34,34,34;
        }

        /* Apply page background and default text color */
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;background:var(--page-bg);color:var(--text-primary)}

        /* Navbar */
        .navbar{background:var(--navbar-bg);border-bottom:1px solid rgba(0,0,0,0.08);box-shadow:0 1px 4px rgba(0,0,0,0.12);backdrop-filter:blur(8px);padding:0;height:60px;position:fixed;top:0;right:0;left:260px;z-index:1000}
        .navbar-brand{font-weight:700;font-size:18px;color:var(--text-primary)!important;display:flex;align-items:center;gap:10px;padding:12px 20px}
        .navbar-brand img{width:32px;height:32px}
        .navbar .nav-link{color:rgba(var(--nav-link-rgb),0.85)!important;font-size:14px;padding:8px 16px!important;transition:all .2s}
        .navbar .nav-link:hover{color:var(--text-primary)!important}
        .navbar .dropdown-menu{background:var(--navbar-bg)!important;border:1px solid var(--border-color)!important;backdrop-filter:blur(8px);border-radius:8px}
        .navbar .dropdown-item{color:var(--text-primary)!important;font-size:13px;padding:8px 16px!important}
        .navbar .dropdown-item:hover{background:var(--surface-contrast)!important;color:var(--text-primary)!important}

        /* Sidebar */
        .sidebar{background:var(--sidebar-bg);box-shadow:0 4px 12px rgba(0,0,0,0.15);position:fixed;left:0;top:0;width:260px;height:100vh;overflow-y:auto;padding:12px 0 24px 0;border-right:1px solid var(--border-color)}
        .sidebar .sidebar-brand{display:flex;align-items:center;gap:10px;padding:12px 20px;margin-bottom:8px;border-radius:8px}
        .sidebar .sidebar-brand img{width:36px;height:36px}
        .sidebar .sidebar-actions{margin-left:auto;display:flex;gap:8px;align-items:center}
        .sidebar .nav-link{color:var(--text-primary);border-left:3px solid transparent;padding:12px 20px;font-size:14px;transition:all .2s;display:flex;align-items:center;gap:10px}
        .sidebar .nav-link i{font-size:16px;width:20px}
        .sidebar .nav-link:hover{background-color:var(--bg-light);border-left-color:var(--primary-color);color:var(--primary-color)}
        .sidebar .nav-link.active{background-color:rgba(9,105,218,0.1);border-left-color:var(--primary-color);color:var(--primary-color);font-weight:600}
        .sidebar .nav-section{padding:16px 20px 8px;font-size:11px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px;margin-top:8px}

        /* Main content */
        .main-content{margin-left:260px;margin-top:60px;padding:32px;min-height:calc(100vh - 60px);background:transparent;color:var(--text-primary)}
        .page-title{color:var(--text-primary);margin-bottom:32px;font-weight:600;font-size:28px;display:flex;align-items:center;gap:12px}
        .page-title i{font-size:32px;color:var(--success-color)}

        /* Cards */
        .card{background:var(--card-bg);border:none;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.06);margin-bottom:24px;transition:all .2s}
        .card-header{background:linear-gradient(135deg,var(--primary-color) 0%,var(--primary-dark) 100%);color:white;border-radius:12px 12px 0 0;padding:20px;font-weight:600;border:none}
        .card-body{padding:24px}

        .stats-card{background:var(--card-bg);border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.06);border-left:4px solid var(--primary-color);transition:all .2s}
        .stats-card h6{color:var(--text-secondary);font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;font-weight:600}
        .stats-card .number{font-size:28px;font-weight:700;color:var(--primary-color)}

        /* Tables */
        .table{background:var(--card-bg);border-radius:12px;overflow:hidden;margin-bottom:0}
        .table thead{background:var(--surface-contrast);border-bottom:1px solid var(--border-color)}
        .table th{color:var(--text-secondary);font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.5px;padding:16px;border:none}
        .table td{padding:14px 16px;border-bottom:1px solid var(--border-color);vertical-align:middle}
        .table tbody tr:hover{background-color:var(--bg-light)}

        /* Forms, buttons, alerts, badges - rely on variables */
        .form-label{font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:8px}
        .form-control{border:1px solid var(--border-color);border-radius:6px;padding:10px 12px;font-size:14px}
        .btn-primary{background-color:var(--primary-color);color:white}
        .btn-outline-primary{border:1px solid var(--border-color);color:var(--primary-color)}

        /* Scrollbar */
        .sidebar::-webkit-scrollbar{width:6px}
        .sidebar::-webkit-scrollbar-track{background:transparent}
        .sidebar::-webkit-scrollbar-thumb{background:rgba(0,0,0,0.12);border-radius:3px}
        .sidebar::-webkit-scrollbar-thumb:hover{background:rgba(0,0,0,0.18)}

        /* Responsive */
        @media (max-width:768px){
            .sidebar{width:100%;height:auto;position:relative;top:0;padding:16px 0;border-right:none;border-bottom:1px solid var(--border-color)}
            .main-content{margin-left:0;margin-top:0;padding:20px}
            .page-title{font-size:20px}
            .stats-card{margin-bottom:16px}
            .card{margin-bottom:16px}
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: rgba(255,255,255,0.3);">
                <span class="navbar-toggler-icon" style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.7%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e\");"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if(auth()->user()->role === 'customer')
                        @php
                            $customer = \App\Models\Customer::where('email', auth()->user()->email)->first();
                            $overdueCount = 0;
                            if ($customer) {
                                $overdueInvoices = \App\Models\Invoice::whereIn('booking_id', $customer->bookings()->pluck('id'))
                                    ->where('status', '!=', 'paid')
                                    ->get()
                                    ->filter(function($invoice) {
                                        return $invoice->status === 'overdue' || now()->isAfter($invoice->due_date);
                                    });
                                $overdueCount = $overdueInvoices->count();
                            }
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.dashboard') }}" title="Dashboard">
                                <i class="bi bi-bell"></i>
                                @if($overdueCount > 0)
                                    <span class="badge bg-danger ms-1">{{ $overdueCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            @if(auth()->user()->role === 'admin')
                                <span class="badge bg-danger ms-1" style="font-size: 10px;">Admin</span>
                            @elseif(auth()->user()->role === 'technician')
                                <span class="badge bg-warning text-dark ms-1" style="font-size: 10px;">Teknisi</span>
                            @else
                                <span class="badge bg-info ms-1" style="font-size: 10px;">Pelanggan</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-header" style="font-size: 12px; color: rgba(255,255,255,0.6);">{{ auth()->user()->email }}</span></li>
                            <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                            @if(auth()->user()->role === 'customer')
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a></li>
                            @elseif(auth()->user()->role === 'technician')
                                <li><a class="dropdown-item" href="{{ route('technician.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('profile.editProfile') }}">
                                <i class="bi bi-person"></i> Edit Profile
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.editEmail') }}">
                                <i class="bi bi-envelope"></i> Ubah Email
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.editPassword') }}">
                                <i class="bi bi-key"></i> Ubah Password
                            </a></li>
                            <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                            <li><form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item" style="border: none; background: none; cursor: pointer;">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('img/jasaku-logo.svg') }}" alt="JASAKU Logo">
            <div style="font-weight:700; font-size:18px; color: var(--text-primary);">JASAKU</div>
            <div class="sidebar-actions">
                <button id="themeToggle" class="btn btn-sm btn-outline-primary" title="Toggle theme">
                    <i class="bi bi-moon" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <nav class="nav flex-column">
            @if(auth()->user()->role === 'admin')
                <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <!-- DATA MASTER SECTION -->
                <div class="nav-section">Data Master</div>
                <a class="nav-link @if(request()->routeIs('customers.*')) active @endif" href="{{ route('customers.index') }}">
                    <i class="bi bi-people"></i> Pelanggan
                </a>
                <a class="nav-link @if(request()->routeIs('technicians.*')) active @endif" href="{{ route('technicians.index') }}">
                    <i class="bi bi-tools"></i> Teknisi
                </a>
                <a class="nav-link @if(request()->routeIs('services.*')) active @endif" href="{{ route('services.index') }}">
                    <i class="bi bi-wrench"></i> Layanan
                </a>

                <!-- TRANSAKSI & KEUANGAN SECTION -->
                <div class="nav-section">Transaksi & Keuangan</div>
                <a class="nav-link @if(request()->routeIs('bookings.*')) active @endif" href="{{ route('bookings.index') }}">
                    <i class="bi bi-calendar-check"></i> Pemesanan
                </a>
                <a class="nav-link @if(request()->routeIs('invoices.*')) active @endif" href="{{ route('invoices.index') }}">
                    <i class="bi bi-receipt"></i> Invoice
                </a>
                @php
                    $pendingPaymentCount = \App\Models\Payment::where('status', 'pending_approval')->count();
                @endphp
                <a class="nav-link @if(request()->routeIs('payments.pending')) active @endif" href="{{ route('payments.pending') }}">
                    <i class="bi bi-credit-card"></i> Monitoring Pembayaran
                    @if($pendingPaymentCount > 0)
                        <span class="badge bg-danger ms-2" style="margin-left: auto !important;">{{ $pendingPaymentCount }}</span>
                    @endif
                </a>

                <!-- REPORTS & ANALYTICS SECTION -->
                <div class="nav-section">Laporan & Analitik</div>
                <a class="nav-link @if(request()->routeIs('reports.dashboard')) active @endif" href="{{ route('reports.dashboard') }}">
                    <i class="bi bi-graph-up"></i> Ringkasan Laporan
                </a>
                <a class="nav-link @if(request()->routeIs('reports.revenue')) active @endif" href="{{ route('reports.revenue') }}">
                    <i class="bi bi-cash-coin"></i> Pendapatan
                </a>
                <a class="nav-link @if(request()->routeIs('reports.bookings')) active @endif" href="{{ route('reports.bookings') }}">
                    <i class="bi bi-bar-chart"></i> Pemesanan
                </a>
                <a class="nav-link @if(request()->routeIs('reports.technicians')) active @endif" href="{{ route('reports.technicians') }}">
                    <i class="bi bi-person-check"></i> Performa Teknisi
                </a>
                <a class="nav-link @if(request()->routeIs('reports.customers')) active @endif" href="{{ route('reports.customers') }}">
                    <i class="bi bi-people-fill"></i> Analisis Pelanggan
                </a>
                <a class="nav-link @if(request()->routeIs('reports.payments')) active @endif" href="{{ route('reports.payments') }}">
                    <i class="bi bi-wallet-fill"></i> Laporan Pembayaran
                </a>

                <!-- KONTEN SECTION -->
                <div class="nav-section">Konten</div>
                <a class="nav-link @if(request()->routeIs('sliders.*')) active @endif" href="{{ route('sliders.index') }}">
                    <i class="bi bi-image"></i> Slider Promosi
                </a>
            @elseif(auth()->user()->role === 'technician')
                <!-- Technician Sidebar -->
                <a class="nav-link @if(request()->routeIs('technician.dashboard')) active @endif" href="{{ route('technician.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-section">Pekerjaan Saya</div>
                <a class="nav-link @if(request()->routeIs('technician.bookings.*')) active @endif" href="{{ route('technician.bookings.index') }}">
                    <i class="bi bi-calendar-check"></i> Pesanan Masuk
                </a>
                <a class="nav-link @if(request()->routeIs('technician.tasks.*')) active @endif" href="{{ route('technician.tasks.index') }}">
                    <i class="bi bi-list-check"></i> Tugas Saya
                </a>
                <div class="nav-section">Akun Saya</div>
                <a class="nav-link @if(request()->routeIs('technician.profile.*')) active @endif" href="{{ route('technician.profile.show') }}">
                    <i class="bi bi-person"></i> Profil
                </a>
                <a class="nav-link @if(request()->routeIs('technician.earnings.*')) active @endif" href="{{ route('technician.earnings.index') }}">
                    <i class="bi bi-wallet2"></i> Penghasilan
                </a>
                <a class="nav-link @if(request()->routeIs('technician.ratings.*')) active @endif" href="{{ route('technician.ratings.index') }}">
                    <i class="bi bi-star"></i> Rating & Review
                </a>
            @else
                <!-- Customer Sidebar -->
                <a class="nav-link @if(request()->routeIs('customer.dashboard')) active @endif" href="{{ route('customer.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-section">Transaksi Saya</div>
                <a class="nav-link @if(request()->routeIs('customer.bookings.*')) active @endif" href="{{ route('customer.bookings.index') }}">
                    <i class="bi bi-calendar-check"></i> Pemesanan Saya
                </a>
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalInvoice" style="cursor: pointer;">
                    <i class="bi bi-receipt"></i> Invoice Saya
                </a>
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalLayanan" style="cursor: pointer;">
                    <i class="bi bi-wrench"></i> Layanan Tersedia
                </a>
            @endif
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="bi bi-exclamation-circle"></i> Error!</strong> Ada kesalahan pada form Anda:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="background: rgba(218, 54, 51, 0.5);"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            const toggle = document.getElementById('themeToggle');
            const saved = localStorage.getItem('theme') || 'dark';
            if(saved === 'light') document.body.classList.add('theme-light');
            function setIcon(){
                if(!toggle) return;
                const i = toggle.querySelector('i');
                if(document.body.classList.contains('theme-light')){
                    i.classList.remove('bi-moon');
                    i.classList.add('bi-sun');
                } else {
                    i.classList.remove('bi-sun');
                    i.classList.add('bi-moon');
                }
            }
            setIcon();
            if(toggle){
                toggle.addEventListener('click', function(){
                    document.body.classList.toggle('theme-light');
                    const theme = document.body.classList.contains('theme-light') ? 'light' : 'dark';
                    localStorage.setItem('theme', theme);
                    setIcon();
                });
            }
        })();
    </script>
    @yield('extra-js')
</body>
</html>
