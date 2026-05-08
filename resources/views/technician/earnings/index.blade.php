@extends('layouts.app')

@section('title', 'Penghasilan Saya - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-wallet2"></i>
        <h1>Penghasilan Saya</h1>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Penghasilan</h6>
                            <h3 class="mb-0">Rp {{ number_format($stats['total_earnings'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #28a745; opacity: 0.2;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #0066cc;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Bulan Ini</h6>
                            <h3 class="mb-0">Rp {{ number_format($stats['this_month_earnings'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #0066cc; opacity: 0.2;">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Rata-rata Per Job</h6>
                            <h3 class="mb-0">Rp {{ number_format($stats['average_job_value'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #ffc107; opacity: 0.2;">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #ff6b35;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Menunggu Pembayaran</h6>
                            <h3 class="mb-0">Rp {{ number_format($stats['pending_payment'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #ff6b35; opacity: 0.2;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mb-3">
        <a href="{{ route('technician.earnings.details') }}" class="btn btn-outline-primary">
            <i class="bi bi-graph-up"></i> Lihat Detail Lengkap
        </a>
        <a href="{{ route('technician.earnings.export') }}" class="btn btn-outline-secondary">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    <!-- Recent Bookings -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Pekerjaan Terbaru yang Selesai</h5>
        </div>

        @if($recentBookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 15%;">Layanan</th>
                            <th style="width: 20%;">Pelanggan</th>
                            <th style="width: 15%;">Harga</th>
                            <th style="width: 15%;">Status Bayar</th>
                            <th style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                            <tr>
                                <td>
                                    {{ $booking->updated_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    {{ $booking->service->name }}
                                </td>
                                <td>
                                    {{ $booking->customer->name ?? '-' }}
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($booking->invoice)
                                        <span class="badge @if($booking->invoice->status === 'paid') bg-success @else bg-warning @endif">
                                            {{ ucfirst($booking->invoice->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('technician.tasks.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <span class="text-muted small">Menampilkan {{ $recentBookings->firstItem() }} - {{ $recentBookings->lastItem() }} dari {{ $recentBookings->total() }} pekerjaan</span>
                {{ $recentBookings->links() }}
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #999;"></i>
                <h5 class="mt-3">Belum ada pekerjaan selesai</h5>
                <p class="text-muted">Pekerjaan yang Anda selesaikan akan ditampilkan di sini.</p>
            </div>
        @endif
    </div>
@endsection
