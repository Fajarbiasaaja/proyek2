@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-title">
        <i class="bi bi-speedometer2"></i>
        <h1>Dashboard</h1>
    </div>

    <!-- Slider Carousel -->
    @if($sliders->count() > 0)
        <div class="slider-container">
            <div id="sliderCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner">
                    @foreach($sliders as $index => $slider)
                        <div class="carousel-item @if($index === 0) active @endif">
                            <div class="slider-wrapper">
                                <img src="{{ asset('storage/' . $slider->image) }}" 
                                     alt="{{ $slider->title }}" 
                                     class="slider-image">
                                
                                <div class="slider-overlay"></div>
                                
                                <div class="slider-content">
                                    @if($slider->title)
                                        <h1 class="slider-title">{{ $slider->title }}</h1>
                                    @endif
                                    
                                    @if($slider->description)
                                        <p class="slider-description">{{ $slider->description }}</p>
                                    @endif
                                    
                                    @if($slider->button_text && $slider->button_link)
                                        <a href="{{ $slider->button_link }}" class="btn btn-primary btn-lg">
                                            {{ $slider->button_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($sliders->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#sliderCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#sliderCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>

                    <div class="carousel-indicators">
                        @foreach($sliders as $index => $slider)
                            <button type="button" data-bs-target="#sliderCarousel" data-bs-slide-to="{{ $index }}" 
                                    @if($index === 0) class="active" @endif aria-label="Slide {{ $index + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <style>
            .slider-container {
                margin-bottom: 30px;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            }

            .slider-wrapper {
                position: relative;
                width: 100%;
                max-height: 500px;
                overflow: hidden;
            }

            .slider-image {
                width: 100%;
                height: 500px;
                object-fit: cover;
                display: block;
            }

            .slider-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 50%);
            }

            .slider-content {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 40px;
                color: white;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                height: 100%;
            }

            .slider-title {
                font-size: 2.5rem;
                font-weight: bold;
                margin-bottom: 15px;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                animation: slideInUp 0.8s ease-out;
            }

            .slider-description {
                font-size: 1.1rem;
                margin-bottom: 25px;
                text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
                max-width: 600px;
                animation: slideInUp 0.8s ease-out 0.2s both;
            }

            .slider-content .btn {
                width: fit-content;
                animation: slideInUp 0.8s ease-out 0.4s both;
            }

            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .carousel-control-prev,
            .carousel-control-next {
                width: 50px;
                height: 50px;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(0, 0, 0, 0.5);
                border-radius: 50%;
                opacity: 0.7;
                transition: opacity 0.3s;
            }

            .carousel-control-prev:hover,
            .carousel-control-next:hover {
                opacity: 1;
            }

            .carousel-indicators {
                bottom: 20px;
                gap: 8px;
            }

            .carousel-indicators button {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.5);
                border: none;
            }

            .carousel-indicators button.active {
                background: white;
            }

            .carousel-fade .carousel-item {
                opacity: 0;
                transition-property: opacity;
                transition-duration: 0.6s;
            }

            .carousel-fade .carousel-item.active {
                opacity: 1;
            }

            @media (max-width: 768px) {
                .slider-image {
                    height: 350px;
                }

                .slider-content {
                    padding: 25px;
                }

                .slider-title {
                    font-size: 1.8rem;
                    margin-bottom: 10px;
                }

                .slider-description {
                    font-size: 0.95rem;
                    margin-bottom: 15px;
                }

                .slider-content .btn {
                    font-size: 0.9rem;
                    padding: 0.6rem 1.5rem;
                }
            }

            @media (max-width: 480px) {
                .slider-image {
                    height: 250px;
                }

                .slider-content {
                    padding: 15px;
                }

                .slider-title {
                    font-size: 1.3rem;
                    margin-bottom: 8px;
                }

                .slider-description {
                    font-size: 0.85rem;
                    margin-bottom: 12px;
                    display: none;
                }

                .carousel-control-prev,
                .carousel-control-next {
                    width: 40px;
                    height: 40px;
                }
            }
        </style>
    @endif

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <h5>Total Pelanggan</h5>
                <div class="number">{{ $totalCustomers }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <h5>Total Pemesanan</h5>
                <div class="number">{{ $totalBookings }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card success">
                <h5>Pemesanan Selesai</h5>
                <div class="number">{{ $completedBookings }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <h5>Pemesanan Menunggu</h5>
                <div class="number">{{ $pendingBookings }}</div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card success">
                <h5>Pendapatan (Dibayar)</h5>
                <div class="number">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <h5>Teknisi Tersedia</h5>
                <div class="number">{{ $availableTechnicians }}</div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-calendar-check"></i> Pemesanan Terbaru</div>
        <div class="card-body">
            @if($recentBookings->isEmpty())
                <p class="text-muted mb-0">Tidak ada pemesanan.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Teknisi</th>
                                <th>Tanggal Jadwal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td>{{ $booking->customer->name }}</td>
                                    <td>{{ $booking->service->name }}</td>
                                    <td>{{ $booking->technician ? $booking->technician->name : '-' }}</td>
                                    <td>{{ $booking->scheduled_date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge @switch($booking->status)
                                            @case('pending') pending @break
                                            @case('confirmed') confirmed @break
                                            @case('in_progress') in_progress @break
                                            @case('completed') completed @break
                                            @case('cancelled') cancelled @break
                                        @endswitch">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Technician Stats -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-tools"></i> Statistik Teknisi
                </div>
                <div class="card-body">
                    @if($technicianStats->isEmpty())
                        <p class="text-muted mb-0">Tidak ada teknisi.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Teknisi</th>
                                        <th>Status</th>
                                        <th>Total Pekerjaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($technicianStats as $technician)
                                        <tr>
                                            <td>{{ $technician->name }}</td>
                                            <td>
                                                <span class="badge @if($technician->status === 'available') bg-success @elseif($technician->status === 'busy') bg-warning @else bg-danger @endif">
                                                    {{ ucfirst($technician->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $technician->bookings_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
