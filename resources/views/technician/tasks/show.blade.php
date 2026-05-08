@extends('layouts.app')

@section('title', 'Detail Tugas #' . $booking->id)

@section('content')
    <div class="page-title">
        <i class="bi bi-list-check"></i>
        <h1>Detail Tugas #{{ $booking->id }}</h1>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Task Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Tugas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">ID Tugas</label>
                                <p class="mb-0"><strong>#{{ $booking->id }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Layanan</label>
                                <p class="mb-0"><strong>{{ $booking->service->name }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Harga</label>
                                <p class="mb-0"><strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Jadwal</label>
                                <p class="mb-0"><strong>{{ $booking->scheduled_date->format('d/m/Y H:i') }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <p class="mb-0">
                                    <span class="badge @if($booking->status === 'confirmed') bg-info @elseif($booking->status === 'in_progress') bg-warning text-dark @endif">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($booking->notes)
                        <div class="mt-3 pt-3 border-top">
                            <label class="text-muted small">Catatan Pelanggan</label>
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Data Pelanggan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Nama</label>
                                <p class="mb-0"><strong>{{ $booking->customer->name }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Email</label>
                                <p class="mb-0"><strong>{{ $booking->customer->email }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Telepon</label>
                                <p class="mb-0"><strong>{{ $booking->customer->phone ?? '-' }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Alamat</label>
                                <p class="mb-0"><strong>{{ $booking->customer->address ?? '-' }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('technician.tasks.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                        @if($booking->status === 'confirmed')
                            <form action="{{ route('technician.tasks.start', $booking) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-play-fill"></i> Mulai Pekerjaan
                                </button>
                            </form>
                        @endif

                        @if($booking->status === 'in_progress')
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                                <i class="bi bi-check-lg"></i> Selesaikan Pekerjaan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <p class="mb-0"><small class="text-muted">Dibuat</small></p>
                                <p class="mb-0"><strong>{{ $booking->created_at->format('d/m/Y H:i') }}</strong></p>
                            </div>
                        </div>

                        @if($booking->status !== 'pending')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <p class="mb-0"><small class="text-muted">Dikonfirmasi</small></p>
                                    <p class="mb-0"><strong>{{ $booking->updated_at->format('d/m/Y H:i') }}</strong></p>
                                </div>
                            </div>
                        @endif

                        @if($booking->status === 'in_progress' || $booking->status === 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <p class="mb-0"><small class="text-muted">Sedang Dikerjakan</small></p>
                                    <p class="mb-0"><strong>{{ $booking->updated_at->format('d/m/Y H:i') }}</strong></p>
                                </div>
                            </div>
                        @endif

                        @if($booking->status === 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <p class="mb-0"><small class="text-muted">Selesai</small></p>
                                    <p class="mb-0"><strong>{{ $booking->updated_at->format('d/m/Y H:i') }}</strong></p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <p class="mb-1"><small class="text-muted">Estimasi Durasi</small></p>
                            <p class="mb-0"><strong>2 Jam</strong></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1"><small class="text-muted">Prioritas</small></p>
                            <p class="mb-0"><strong>Normal</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Task Modal -->
    @if($booking->status === 'in_progress')
        <div class="modal fade" id="completeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Selesaikan Tugas #{{ $booking->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('technician.tasks.complete', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Catatan Penyelesaian <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="completion_notes" rows="5" required placeholder="Jelaskan pekerjaan yang telah dilakukan..."></textarea>
                                @error('completion_notes')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg"></i> Konfirmasi Selesai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .timeline {
            position: relative;
            padding: 0;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 20px;
            position: relative;
        }

        .timeline-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 5px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 15px;
            width: 2px;
            height: calc(100% + 20px);
            background: #ddd;
        }

        .timeline-content p {
            margin: 0;
            font-size: 14px;
        }
    </style>
@endsection
