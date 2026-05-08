@extends('layouts.app')

@section('title', 'Analisis Pelanggan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-people-fill"></i> Analisis Pelanggan
        </h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="stats-card">
                <h6>Pelanggan Baru</h6>
                <div class="number">{{ $summary['new_customers'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="stats-card">
                <h6>Pelanggan Kembali</h6>
                <div class="number">{{ $summary['returning_customers'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Period Info -->
    <div class="card mb-4">
        <div class="card-body">
            <p style="font-size: 14px; color: var(--text-secondary); margin: 0;">
                <strong>Period:</strong> {{ $period['start_date'] }} - {{ $period['end_date'] }}
            </p>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-graph-up"></i> Top 10 Pelanggan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Pelanggan</th>
                            <th>Jumlah Pemesanan</th>
                            <th>Total Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($top_customers as $customer)
                        <tr>
                            <td>{{ $customer->name ?? 'N/A' }}</td>
                            <td>{{ $customer->total_bookings ?? 0 }}</td>
                            <td>Rp {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data pelanggan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
