@extends('layouts.app')

@section('title', 'Performa Teknisi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-person-check"></i> Laporan Performa Teknisi
        </h1>
    </div>

    <!-- Period Info -->
    <div class="card mb-4">
        <div class="card-body">
            <p style="font-size: 14px; color: var(--text-secondary); margin: 0;">
                <strong>Period:</strong> {{ $period['start_date'] }} - {{ $period['end_date'] }}
            </p>
        </div>
    </div>

    <!-- Technician Performance Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-table"></i> Daftar Teknisi & Performa
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Teknisi</th>
                            <th>Total Pekerjaan</th>
                            <th>Pekerjaan Selesai</th>
                            <th>Tingkat Penyelesaian</th>
                            <th>Rating Rata-rata</th>
                            <th>Total Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($technicians as $tech)
                        <tr>
                            <td>{{ $tech['name'] ?? 'N/A' }}</td>
                            <td>{{ $tech['total_jobs'] ?? 0 }}</td>
                            <td>{{ $tech['completed_jobs'] ?? 0 }}</td>
                            <td>
                                <span class="badge bg-{{ $tech['completion_rate'] >= 80 ? 'success' : ($tech['completion_rate'] >= 50 ? 'warning' : 'danger') }}">
                                    {{ $tech['completion_rate'] ?? 0 }}%
                                </span>
                            </td>
                            <td>
                                <span class="text-warning">
                                    @for($i = 0; $i < round($tech['avg_rating'] ?? 0); $i++)
                                        <i class="bi bi-star-fill"></i>
                                    @endfor
                                    ({{ $tech['avg_rating'] ?? 0 }})
                                </span>
                            </td>
                            <td>{{ $tech['total_ratings'] ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada data teknisi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
