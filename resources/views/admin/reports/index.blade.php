@extends('layouts.app')

@section('title', 'Laporan & Analitik')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">
                    <i class="fas fa-chart-line me-2"></i>Laporan & Analitik
                </h1>
                <small class="text-muted">Data dan statistik kinerja sistem</small>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Total Pendapatan</h6>
                            <h2 class="mb-0">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +5% bulan lalu
                            </small>
                        </div>
                        <i class="fas fa-money-bill-wave" style="font-size: 32px; color: #28a745; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Total Pesanan</h6>
                            <h2 class="mb-0">{{ $stats['total_bookings'] ?? 0 }}</h2>
                            <small class="text-info">
                                {{ $stats['completed_bookings'] ?? 0 }} selesai ({{ $stats['completion_rate'] ?? 0 }}%)
                            </small>
                        </div>
                        <i class="fas fa-tasks" style="font-size: 32px; color: #007bff; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Total Pelanggan</h6>
                            <h2 class="mb-0">{{ $stats['total_customers'] ?? 0 }}</h2>
                            <small class="text-success">
                                <i class="fas fa-plus"></i> +{{ $stats['new_customers_this_month'] ?? 0 }} bulan ini
                            </small>
                        </div>
                        <i class="fas fa-users" style="font-size: 32px; color: #17a2b8; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Rating Rata-rata</h6>
                            <h2 class="mb-0">{{ number_format($stats['avg_rating'] ?? 0, 1) }}/5</h2>
                            <small class="text-warning">
                                <i class="fas fa-star"></i> {{ $stats['total_ratings'] ?? 0 }} rating
                            </small>
                        </div>
                        <i class="fas fa-star" style="font-size: 32px; color: #ffc107; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="revenue-tab" data-bs-toggle="tab" data-bs-target="#revenue" role="tab">
                <i class="fas fa-chart-bar me-2"></i>Laporan Pendapatan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking" role="tab">
                <i class="fas fa-chart-pie me-2"></i>Statistik Pesanan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="technician-tab" data-bs-toggle="tab" data-bs-target="#technician" role="tab">
                <i class="fas fa-tools me-2"></i>Performa Teknisi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" role="tab">
                <i class="fas fa-user-graph me-2"></i>Analisis Pelanggan
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Revenue Report -->
        <div class="tab-pane fade show active" id="revenue" role="tabpanel">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-bottom">
                            <h5 class="mb-0">Pendapatan 12 Bulan Terakhir</h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-bottom">
                            <h5 class="mb-0">Metode Pembayaran</h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Detail Pendapatan Per Layanan</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Layanan</th>
                                <th>Jumlah Pesanan</th>
                                <th>Total Pendapatan</th>
                                <th>Rata-rata Harga</th>
                                <th>% dari Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $serviceRevenue = $stats['revenue_by_service'] ?? [];
                            @endphp
                            @forelse($serviceRevenue as $service)
                                <tr>
                                    <td><strong>{{ $service['name'] }}</strong></td>
                                    <td>{{ $service['count'] }}</td>
                                    <td>Rp {{ number_format($service['total'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($service['average'], 0, ',', '.') }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ $service['percentage'] }}%">
                                                {{ $service['percentage'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Booking Statistics -->
        <div class="tab-pane fade" id="booking" role="tabpanel">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-bottom">
                            <h5 class="mb-0">Status Pesanan</h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="bookingStatusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-bottom">
                            <h5 class="mb-0">Pesanan per Layanan</h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="bookingByServiceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technician Performance -->
        <div class="tab-pane fade" id="technician" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Top 10 Teknisi Terbaik</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama Teknisi</th>
                                <th>Pesanan Selesai</th>
                                <th>Rating Rata-rata</th>
                                <th>Total Pendapatan</th>
                                <th>Tingkat Kepuasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $topTechnicians = $stats['top_technicians'] ?? [];
                            @endphp
                            @forelse($topTechnicians as $index => $tech)
                                <tr>
                                    <td><strong>#{{ $index + 1 }}</strong></td>
                                    <td>{{ $tech['name'] }}</td>
                                    <td>{{ $tech['completed_jobs'] }}</td>
                                    <td>
                                        <div class="text-warning">
                                            @for($i = 0; $i < 5; $i++)
                                                @if($i < floor($tech['rating']))
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                            <span class="text-muted">{{ number_format($tech['rating'], 1) }}/5</span>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($tech['earnings'], 0, ',', '.') }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: {{ $tech['satisfaction'] }}%">
                                                {{ $tech['satisfaction'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Analytics -->
        <div class="tab-pane fade" id="customer" role="tabpanel">
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-bottom">
                            <h5 class="mb-0">Pertumbuhan Pelanggan</h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="customerGrowthChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-bottom">
                            <h5 class="mb-0">Tipe Pelanggan</h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="customerTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h6 class="mb-3">Export Laporan</h6>
            <div class="btn-group" role="group">
                <a href="" class="btn btn-outline-secondary">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="" class="btn btn-outline-secondary">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="" class="btn btn-outline-secondary">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .page-header {
        padding: 20px 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
    }
    
    .nav-tabs .nav-link.active {
        color: #0066cc;
        border-bottom: 3px solid #0066cc;
        background: none;
    }
</style>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: [100000, 150000, 200000, 250000, 300000, 350000, 400000, 450000, 500000, 550000, 600000, 650000],
                borderColor: '#0066cc',
                backgroundColor: 'rgba(0, 102, 204, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#0066cc',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Payment Method Chart
    new Chart(document.getElementById('paymentMethodChart'), {
        type: 'doughnut',
        data: {
            labels: ['Bank Transfer', 'E-Wallet', 'Kartu Kredit', 'Cicilan'],
            datasets: [{
                data: [45, 30, 20, 5],
                backgroundColor: ['#0066cc', '#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Booking Status Chart
    new Chart(document.getElementById('bookingStatusChart'), {
        type: 'bar',
        data: {
            labels: ['Pending', 'Confirmed', 'In Progress', 'Completed', 'Cancelled'],
            datasets: [{
                label: 'Jumlah Pesanan',
                data: [10, 25, 15, 100, 5],
                backgroundColor: ['#ffc107', '#17a2b8', '#0066cc', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Booking by Service Chart
    new Chart(document.getElementById('bookingByServiceChart'), {
        type: 'bar',
        data: {
            labels: ['AC Cleaning', 'AC Repair', 'AC Installation', 'AC Maintenance'],
            datasets: [{
                label: 'Pesanan',
                data: [45, 38, 20, 37],
                backgroundColor: '#0066cc'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

    // Customer Growth Chart
    new Chart(document.getElementById('customerGrowthChart'), {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Pelanggan Baru',
                data: [10, 15, 20, 25],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Customer Type Chart
    new Chart(document.getElementById('customerTypeChart'), {
        type: 'pie',
        data: {
            labels: ['New Customers', 'Repeat Customers', 'Premium Members'],
            datasets: [{
                data: [35, 50, 15],
                backgroundColor: ['#0066cc', '#28a745', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection
