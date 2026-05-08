@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-wallet-fill"></i> Laporan Pembayaran
        </h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Total Pembayaran</h6>
                <div class="number">{{ $summary['total_payments'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Disetujui</h6>
                <div class="number">{{ $summary['approved_payments'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Tertunda</h6>
                <div class="number">{{ $summary['pending_payments'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <h6>Ditolak</h6>
                <div class="number">{{ $summary['rejected_payments'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Total Amount -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 8px;">
                        <strong>Total Jumlah Pembayaran (Disetujui):</strong>
                    </p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--primary-color); margin: 0;">
                        Rp {{ number_format($summary['total_amount'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 8px;">
                        <strong>Period:</strong> {{ $period['start_date'] }} - {{ $period['end_date'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-credit-card"></i> Pembayaran Berdasarkan Metode
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Metode Pembayaran</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($by_method as $method => $data)
                        <tr>
                            <td>{{ ucfirst($method) ?? 'Unknown' }}</td>
                            <td>{{ $data['count'] ?? 0 }}</td>
                            <td>Rp {{ number_format($data['total_amount'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data metode pembayaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
