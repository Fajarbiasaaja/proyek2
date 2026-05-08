@extends('layouts.app')

@section('title', 'Detail Rating - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-star"></i>
        <h1>Detail Rating</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Rating Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Ulasan Pelanggan</h5>
                </div>
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">{{ $rating->booking->customer->name }}</h5>
                            <small class="text-muted">{{ $rating->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <div style="font-size: 2rem;">
                            @for($i = 0; $i < $rating->rating; $i++)
                                <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                            @endfor
                            @for($i = 0; $i < (5 - $rating->rating); $i++)
                                <i class="bi bi-star" style="color: #ddd;"></i>
                            @endfor
                        </div>
                    </div>

                    <!-- Service Info -->
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small">Layanan</label>
                        <p class="mb-0"><strong>{{ $rating->booking->service->name }}</strong></p>
                    </div>

                    <!-- Comment -->
                    @if($rating->comment)
                        <div class="mb-3">
                            <label class="text-muted small">Komentar</label>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $rating->comment }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Booking Details -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Detail Pesanan #{{ $booking->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Jadwal</label>
                                <p class="mb-0"><strong>{{ $booking->scheduled_date->format('d/m/Y H:i') }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Harga</label>
                                <p class="mb-0"><strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <p class="mb-0"><span class="badge bg-success">Selesai</span></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Penyelesaian</label>
                                <p class="mb-0"><strong>{{ $booking->updated_at->format('d/m/Y H:i') }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Rating</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Skor Rating</small>
                        <h3 class="mb-0">{{ $rating->rating }}/5</h3>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Kategori</small>
                        <p class="mb-0">
                            @if($rating->rating >= 4)
                                <strong class="text-success">Sangat Baik</strong>
                            @elseif($rating->rating >= 3)
                                <strong class="text-info">Baik</strong>
                            @else
                                <strong class="text-warning">Cukup</strong>
                            @endif
                        </p>
                    </div>
                    <div>
                        <small class="text-muted">Waktu Rating</small>
                        <p class="mb-0"><strong>{{ $rating->created_at->diffForHumans() }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Action -->
            <div class="mt-3">
                <a href="{{ route('technician.ratings.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Rating
                </a>
            </div>
        </div>
    </div>
@endsection
