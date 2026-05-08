@extends('layouts.app')

@section('title', 'Laporan Pemesanan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-bar-chart"></i> Laporan Pemesanan
        </h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Total Pemesanan</h6>
                <div class="number">{{ $summary['total_bookings'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Selesai</h6>
                <div class="number">{{ $summary['completed'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Tertunda</h6>
                <div class="number">{{ $summary['pending'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Dibatalkan</h6>
                <div class="number">{{ $summary['cancelled'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Rate Cards -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="stats-card">
                <h6>Tingkat Penyelesaian</h6>
                <div class="number">{{ $summary['completion_rate'] ?? '0%' }}</div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="stats-card">
                <h6>Tingkat Pembatalan</h6>
                <div class="number">{{ $summary['cancellation_rate'] ?? '0%' }}</div>
            </div>
        </div>
    </div>

    <!-- Bookings by Service -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-bar-chart"></i> Pemesanan Berdasarkan Layanan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th>Jumlah Pesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($by_service as $service)
                        <tr>
                            <td>{{ $service->name ?? 'N/A' }}</td>
                            <td>{{ $service->count ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Tidak ada data layanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Daily Trend -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-graph-up"></i> Tren Harian (7 Hari Terakhir)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah Pesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daily_trend as $trend)
                        <tr>
                            <td>{{ $trend['date'] }}</td>
                            <td>{{ $trend['count'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
