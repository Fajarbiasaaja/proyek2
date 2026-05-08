@extends('layouts.app')

@section('title', 'Dashboard Teknisi')

@section('content')
    <div class="page-title">
        <i class="bi bi-tools"></i>
        <h1>Dashboard Teknisi</h1>
    </div>

    <!-- Welcome Card -->
    <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #ff6b35 0%, #d94f1a 100%); color: white;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1">Selamat datang, {{ auth()->user()->name }}!</h5>
                <p class="mb-0">Kelola jadwal kerja dan booking Anda dengan mudah melalui dashboard ini.</p>
            </div>
            <div style="font-size: 3rem; opacity: 0.2;">
                <i class="bi bi-tools"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Bookings -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #0066cc;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Booking</h6>
                            <h3 class="mb-0">{{ $stats['total_bookings'] ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #0066cc; opacity: 0.2;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #ff6b35;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Booking Aktif</h6>
                            <h3 class="mb-0">{{ $stats['active_bookings'] ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #ff6b35; opacity: 0.2;">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Bookings -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Selesai</h6>
                            <h3 class="mb-0">{{ $stats['completed_bookings'] ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #28a745; opacity: 0.2;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Rating Rata-rata</h6>
                            <div>
                                @php
                                    $rating = $stats['average_rating'] ?? 0;
                                    $stars = floor($rating);
                                    $hasHalf = ($rating - $stars) >= 0.5;
                                @endphp
                                <span style="font-size: 1.5rem;">
                                    @for($i = 0; $i < $stars; $i++)
                                        <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                    @endfor
                                    @if($hasHalf)
                                        <i class="bi bi-star-half" style="color: #ffc107;"></i>
                                    @endif
                                    @for($i = 0; $i < (5 - $stars - ($hasHalf ? 1 : 0)); $i++)
                                        <i class="bi bi-star" style="color: #ddd;"></i>
                                    @endfor
                                </span>
                                <small class="text-muted d-block mt-1">{{ round($rating, 1) }}/5.0</small>
                            </div>
                        </div>
                        <div style="font-size: 2.5rem; color: #ffc107; opacity: 0.2;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs untuk navigasi -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-content" type="button">
                <i class="bi bi-hourglass-split"></i> Booking Aktif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-content" type="button">
                <i class="bi bi-calendar-week"></i> Jadwal Mendatang
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-content" type="button">
                <i class="bi bi-check-circle"></i> Selesai
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Active Bookings -->
        <div class="tab-pane fade show active" id="active-content">
            @if($activeBookings->count() > 0)
                <div class="row g-3">
                    @foreach($activeBookings as $booking)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm" style="border-left: 4px solid #ff6b35;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">
                                                {{ $booking->booking_number }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i>
                                                {{ $booking->scheduled_date->format('d M Y, H:i') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-warning">{{ ucfirst($booking->status) }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="mb-2" style="font-size: 0.9rem;">
                                            <i class="bi bi-hammer"></i> Layanan
                                        </h6>
                                        <p class="mb-0">{{ $booking->service->name }}</p>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <h6 style="font-size: 0.85rem;" class="text-muted">Pelanggan</h6>
                                            <p class="mb-0" style="font-size: 0.9rem;">{{ $booking->customer->name }}</p>
                                            <small class="text-muted">{{ $booking->customer->phone }}</small>
                                        </div>
                                        <div class="col-6">
                                            <h6 style="font-size: 0.85rem;" class="text-muted">Lokasi</h6>
                                            <p class="mb-0" style="font-size: 0.9rem;">{{ Str::limit($booking->location, 25) }}</p>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('technician.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('technician.bookings.show', $booking) }}#update" class="btn btn-sm btn-primary flex-grow-1">
                                            <i class="bi bi-pencil"></i> Update Status
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info border-0 text-center py-5">
                    <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">Tidak ada booking aktif saat ini. Selamat beristirahat!</p>
                </div>
            @endif
        </div>

        <!-- Upcoming Bookings -->
        <div class="tab-pane fade" id="upcoming-content">
            @if($upcomingBookings->count() > 0)
                <div class="row g-3">
                    @foreach($upcomingBookings as $booking)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm" style="border-left: 4px solid #0066cc;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">
                                                {{ $booking->booking_number }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i>
                                                {{ $booking->scheduled_date->format('d M Y, H:i') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-info">{{ ucfirst($booking->status) }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="mb-2" style="font-size: 0.9rem;">
                                            <i class="bi bi-hammer"></i> Layanan
                                        </h6>
                                        <p class="mb-0">{{ $booking->service->name }}</p>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <h6 style="font-size: 0.85rem;" class="text-muted">Pelanggan</h6>
                                            <p class="mb-0" style="font-size: 0.9rem;">{{ $booking->customer->name }}</p>
                                            <small class="text-muted">{{ $booking->customer->phone }}</small>
                                        </div>
                                        <div class="col-6">
                                            <h6 style="font-size: 0.85rem;" class="text-muted">Lokasi</h6>
                                            <p class="mb-0" style="font-size: 0.9rem;">{{ Str::limit($booking->location, 25) }}</p>
                                        </div>
                                    </div>

                                    <div class="alert alert-light mb-2 p-2">
                                        <small>
                                            <i class="bi bi-info-circle"></i>
                                            Jadwal dalam {{ $booking->scheduled_date->diffForHumans() }}
                                        </small>
                                    </div>

                                    <a href="{{ route('technician.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info border-0 text-center py-5">
                    <i class="bi bi-calendar-check" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">Tidak ada booking yang dijadwalkan ke depannya.</p>
                </div>
            @endif
        </div>

        <!-- Completed Bookings -->
        <div class="tab-pane fade" id="completed-content">
            @if($completedBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th>Nomor Booking</th>
                                <th>Layanan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal Selesai</th>
                                <th>Rating</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedBookings as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking->booking_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $booking->service->name }}
                                    </td>
                                    <td>
                                        {{ $booking->customer->name }}
                                    </td>
                                    <td>
                                        @if($booking->completed_at)
                                            {{ $booking->completed_at->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $bookingRating = $booking->rating ?? 0;
                                            $rStars = floor($bookingRating);
                                            $rHasHalf = ($bookingRating - $rStars) >= 0.5;
                                        @endphp
                                        <span style="font-size: 0.9rem;">
                                            @if($bookingRating > 0)
                                                @for($i = 0; $i < $rStars; $i++)
                                                    <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                                @endfor
                                                @if($rHasHalf)
                                                    <i class="bi bi-star-half" style="color: #ffc107;"></i>
                                                @endif
                                                {{ round($bookingRating, 1) }}
                                            @else
                                                <span class="text-muted">Belum dinilai</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('technician.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($completedBookings->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $completedBookings->links() }}
                    </div>
                @endif
            @else
                <div class="alert alert-info border-0 text-center py-5">
                    <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">Belum ada booking yang diselesaikan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card border-0 shadow-sm mt-4" style="border-top: 4px solid #28a745;">
        <div class="card-body">
            <h6 class="card-title mb-3">
                <i class="bi bi-lightning-fill"></i> Aksi Cepat
            </h6>
            <div class="row g-2">
                <div class="col-md-4">
                    <a href="{{ route('technician.bookings.index') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-list-ul"></i> Lihat Semua Booking
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('profile.editProfile') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-person"></i> Edit Profil
                    </a>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .page-title i {
            font-size: 28px;
            color: #ff6b35;
        }

        .page-title h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
    </style>
@endsection
