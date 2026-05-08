@extends('layouts.app')

@section('title', 'Detail Pesanan - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-calendar-check"></i>
        <h1>Detail Pesanan</h1>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Booking Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Waktu Jadwal</h6>
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-event text-primary"></i>
                                {{ $booking->scheduled_date->format('l, d F Y') }}
                            </h5>
                            <p class="text-muted mb-0">Pukul {{ $booking->scheduled_date->format('H:i') }} WITA</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Status Pesanan</h6>
                            <div>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning text-dark" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                        <i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-info text-white" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                        <i class="bi bi-check-circle"></i> Dikonfirmasi
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge bg-success text-white" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                        <i class="bi bi-check2-circle"></i> Selesai
                                    </span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="badge bg-danger text-white" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                        <i class="bi bi-x-circle"></i> Dibatalkan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0"><i class="bi bi-person-circle"></i> Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Nama Pelanggan:</strong><br>
                                {{ $booking->customer->name }}
                            </p>
                            <p class="mb-0">
                                <strong>Nomor Telepon:</strong><br>
                                <a href="tel:{{ $booking->customer->phone }}">{{ $booking->customer->phone }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Email:</strong><br>
                                <a href="mailto:{{ $booking->customer->email }}">{{ $booking->customer->email }}</a>
                            </p>
                            <p class="mb-0">
                                <strong>Alamat:</strong><br>
                                {{ $booking->customer->address ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0"><i class="bi bi-tools"></i> Detail Layanan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Jenis Layanan:</strong><br>
                                <span class="badge bg-light text-dark" style="padding: 0.5rem 0.75rem;">
                                    {{ $booking->service->name }}
                                </span>
                            </p>
                            <p class="mb-0">
                                <strong>Deskripsi:</strong><br>
                                {{ $booking->service->description ?? '-' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Lokasi Servis:</strong><br>
                                {{ $booking->service_location }}
                            </p>
                            <p class="mb-0">
                                <strong>Catatan Pelanggan:</strong><br>
                                <small>{{ $booking->notes ?? '-' }}</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Information (if exists) -->
            @if($booking->invoice)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0"><i class="bi bi-receipt"></i> Informasi Invoice</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Nomor Invoice:</strong><br>
                                    {{ $booking->invoice->invoice_number }}
                                </p>
                                <p class="mb-0">
                                    <strong>Subtotal:</strong><br>
                                    Rp {{ number_format($booking->invoice->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Pajak (PPN):</strong><br>
                                    Rp {{ number_format($booking->invoice->tax, 0, ',', '.') }}
                                </p>
                                <p class="mb-0">
                                    <strong>Total:</strong><br>
                                    <strong style="color: #28a745;">Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="btn-group w-100" role="group">
                        @if($booking->status === 'pending')
                            <form method="POST" action="{{ route('technician.bookings.accept', $booking) }}" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Apakah Anda yakin ingin menerima pesanan ini?');">
                                    <i class="bi bi-check-circle"></i> Terima Pesanan
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Tolak Pesanan
                            </button>
                        @elseif($booking->status === 'confirmed')
                            <form method="POST" action="{{ route('technician.bookings.markAsCompleted', $booking) }}" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100"
                                        onclick="return confirm('Tandai pesanan ini sebagai selesai?');">
                                    <i class="bi bi-check2-circle"></i> Tandai Selesai
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('technician.bookings.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="mb-3 text-muted">
                        <i class="bi bi-info-circle"></i> Informasi Singkat
                    </h6>
                    <p class="mb-2">
                        <small><strong>ID Pesanan:</strong></small><br>
                        <small class="text-monospace">#{{ $booking->id }}</small>
                    </p>
                    <p class="mb-2">
                        <small><strong>Dibuat:</strong></small><br>
                        <small>{{ $booking->created_at->format('d M Y H:i') }}</small>
                    </p>
                    <p class="mb-0">
                        <small><strong>Diupdate:</strong></small><br>
                        <small>{{ $booking->updated_at->format('d M Y H:i') }}</small>
                    </p>
                </div>
            </div>

            <!-- Ratings (if completed) -->
            @if($booking->status === 'completed' && $booking->ratings->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0"><i class="bi bi-star-fill text-warning"></i> Rating Pelanggan</h6>
                    </div>
                    <div class="card-body">
                        @foreach($booking->ratings as $rating)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong>{{ $rating->rating_type }}</strong>
                                    <span class="text-warning">
                                        @for($i = 0; $i < $rating->rating; $i++)
                                            <i class="bi bi-star-fill"></i>
                                        @endfor
                                    </span>
                                </div>
                                <p class="text-muted mb-0">
                                    <small>{{ $rating->comment ?? '-' }}</small>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('technician.bookings.reject', $booking) }}">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted mb-3">Silakan berikan alasan mengapa Anda menolak pesanan ini.</p>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      name="reason"
                                      rows="4"
                                      placeholder="Jelaskan alasan penolakan..."
                                      required></textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Tolak Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

        .text-monospace {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-radius: 0.375rem 0 0 0.375rem;
        }

        .btn-group .btn:last-child {
            border-radius: 0 0.375rem 0.375rem 0;
        }
    </style>
@endpush
