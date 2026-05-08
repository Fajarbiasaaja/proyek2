@extends('layouts.app')

@section('title', 'Ringkasan Laporan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-graph-up"></i> Ringkasan Laporan
        </h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Total Pendapatan</h6>
                <div class="number">Rp {{ number_format($overview['total_revenue'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Total Pemesanan</h6>
                <div class="number">{{ $overview['total_bookings'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Total Pelanggan</h6>
                <div class="number">{{ $overview['total_customers'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Total Teknisi</h6>
                <div class="number">{{ $overview['total_technicians'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-bar-chart"></i> Tren Pendapatan
                </div>
                <div class="card-body" style="min-height: 300px;">
                    <p class="text-muted"><i class="bi bi-info-circle"></i> Chart data akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pie-chart"></i> Distribusi Status Pemesanan
                </div>
                <div class="card-body" style="min-height: 300px;">
                    <p class="text-muted"><i class="bi bi-info-circle"></i> Chart data akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-calendar-check"></i> Pemesanan Terbaru
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Teknisi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                            <td>{{ $booking->technician->name ?? 'Belum Ditugaskan' }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($booking->status) }}</span>
                            </td>
                            <td>{{ $booking->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data pemesanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
