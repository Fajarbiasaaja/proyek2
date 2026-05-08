@extends('layouts.app')

@section('title', 'Detail Penghasilan - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-wallet2"></i>
        <h1>Detail Penghasilan Lengkap</h1>
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Selesai</h6>
                            <h3 class="mb-0">{{ $stats['total_completed'] ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #28a745; opacity: 0.2;">
                            <i class="bi bi-check-circle"></i>
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
                            <h6 class="text-muted mb-1">Total Penghasilan</h6>
                            <h3 class="mb-0">Rp {{ number_format($stats['total_earnings'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #0066cc; opacity: 0.2;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Penghasilan Berdasarkan Layanan</h6>
                    <div class="row">
                        @forelse($stats['by_service'] ?? [] as $service => $data)
                            <div class="col-6 mb-2">
                                <small class="text-muted">{{ $service }}</small>
                                <p class="mb-0">{{ $data['count'] }} job - Rp {{ number_format($data['total'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted small">Belum ada data</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mb-3">
        <a href="{{ route('technician.earnings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('technician.earnings.export') }}" class="btn btn-outline-primary">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    <!-- Detailed Bookings -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table"></i> Rincian Pekerjaan</h5>
        </div>

        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-sm">
                    <thead>
                        <tr>
                            <th style="width: 12%;">No</th>
                            <th style="width: 12%;">Tanggal</th>
                            <th style="width: 15%;">Layanan</th>
                            <th style="width: 18%;">Pelanggan</th>
                            <th style="width: 12%;">Harga</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 7%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $index => $booking)
                            <tr>
                                <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}</td>
                                <td>{{ $booking->updated_at->format('d/m/Y') }}</td>
                                <td>{{ $booking->service->name }}</td>
                                <td>{{ $booking->customer->name ?? '-' }}</td>
                                <td><strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
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
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <span class="text-muted small">Menampilkan {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }} dari {{ $bookings->total() }} pekerjaan</span>
                {{ $bookings->links() }}
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #999;"></i>
                <h5 class="mt-3">Belum ada pekerjaan selesai</h5>
                <p class="text-muted">Rincian pekerjaan yang selesai akan ditampilkan di sini.</p>
            </div>
        @endif
    </div>
@endsection
