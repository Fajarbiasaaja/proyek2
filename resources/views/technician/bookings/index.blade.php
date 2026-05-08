@extends('layouts.app')

@section('title', 'Pesanan Masuk - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-calendar-check"></i>
        <h1>Pesanan Masuk</h1>
    </div>

    @if($bookings->count() > 0)
        <!-- Bookings Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 25%;">Pelanggan</th>
                            <th style="width: 20%;">Layanan</th>
                            <th style="width: 15%;">Lokasi</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        {{ $booking->scheduled_date->format('d M Y') }}
                                        <br>
                                        <strong>{{ $booking->scheduled_date->format('H:i') }}</strong>
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $booking->customer->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $booking->customer->phone }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $booking->service->name }}</span>
                                </td>
                                <td>
                                    <small>{{ Str::limit($booking->service_location, 25) }}</small>
                                </td>
                                <td>
                                    @if($booking->status === 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="badge bg-info text-white">Dikonfirmasi</span>
                                    @elseif($booking->status === 'completed')
                                        <span class="badge bg-success text-white">Selesai</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="badge bg-danger text-white">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $booking->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('technician.bookings.show', $booking) }}" 
                                           class="btn btn-outline-primary" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($booking->status === 'pending')
                                            <form method="POST" 
                                                  action="{{ route('technician.bookings.accept', $booking) }}"
                                                  class="d-inline"
                                                  style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm"
                                                        title="Terima Pesanan"
                                                        onclick="return confirm('Terima pesanan ini?');">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $bookings->links('pagination::bootstrap-4') }}
        </div>
    @else
        <!-- Empty State -->
        <div class="card border-0 shadow-sm text-center py-5">
            <div style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;">
                <i class="bi bi-inbox"></i>
            </div>
            <h5 class="text-muted">Tidak Ada Pesanan Masuk</h5>
            <p class="text-muted">Anda belum memiliki pesanan yang ditugaskan saat ini.</p>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .page-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .page-title i {
            font-size: 2rem;
            color: #0066cc;
        }

        .page-title h1 {
            font-size: 1.8rem;
            margin: 0;
            color: #333;
        }

        .btn-group-sm .btn {
            padding: 0.35rem 0.5rem;
            font-size: 0.875rem;
        }

        table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }

        table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 0.5rem 0.75rem;
        }
    </style>
@endpush
