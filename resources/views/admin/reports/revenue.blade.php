@extends('layouts.app')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-cash-coin"></i> Laporan Pendapatan
        </h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <h6>Total Pendapatan</h6>
                <div class="number">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <h6>Pendapatan Terbayar</h6>
                <div class="number">Rp {{ number_format($summary['paid_revenue'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <h6>Pendapatan Tertunda</h6>
                <div class="number">Rp {{ number_format($summary['pending_revenue'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <p style="font-size: 14px; color: var(--text-secondary);">
                <strong>Period:</strong> {{ $period['start_date'] }} - {{ $period['end_date'] }}
            </p>
        </div>
    </div>

    <!-- Revenue by Service -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-bar-chart"></i> Pendapatan Berdasarkan Layanan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th>Jumlah Pesanan</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenue_by_service as $service)
                        <tr>
                            <td>{{ $service->name ?? 'N/A' }}</td>
                            <td>{{ $service->total_bookings ?? 0 }}</td>
                            <td>Rp {{ number_format($service->revenue ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data layanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-pie-chart"></i> Status Pembayaran
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payment_status as $status)
                        <tr>
                            <td>
                                <span class="badge bg-{{ $status->status === 'paid' ? 'success' : ($status->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($status->status) }}
                                </span>
                            </td>
                            <td>{{ $status->count ?? 0 }}</td>
                            <td>Rp {{ number_format($status->total ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data pembayaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
