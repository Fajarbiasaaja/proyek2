@extends('layouts.app')

@section('title', 'Layanan Tersedia')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">
                    <i class="fas fa-tools me-2"></i>Layanan Tersedia
                </h1>
                <small class="text-muted">Jelajahi dan pilih layanan yang sesuai kebutuhan Anda</small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Pesan Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari layanan..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select" onchange="window.location.href='?sort=' + this.value">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Harga Termurah</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nama A-Z</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @php
                            $categories = \App\Models\Service::distinct()->pluck('category');
                        @endphp
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    @if($services && $services->count() > 0)
        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 service-card">
                        <!-- Service Image/Icon -->
                        <div class="card-header bg-light border-0" style="height: 200px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px 10px 0 0;">
                            <i class="fas fa-snowflake" style="font-size: 64px; color: white; opacity: 0.8;"></i>
                        </div>

                        <div class="card-body">
                            <!-- Service Name -->
                            <h5 class="card-title mb-2">{{ $service->name }}</h5>

                            <!-- Service Description -->
                            <p class="card-text text-muted small mb-3">
                                {{ substr($service->description, 0, 100) }}{{ strlen($service->description) > 100 ? '...' : '' }}
                            </p>

                            <!-- Service Details -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-hourglass-half"></i> Durasi
                                    </small>
                                    <strong>{{ $service->duration_minutes }} Menit</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> Kategori
                                    </small>
                                    <span class="badge bg-light text-dark">{{ $service->category ?? 'General' }}</span>
                                </div>
                                @if($service->warranty_months)
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt"></i> Garansi
                                        </small>
                                        <strong>{{ $service->warranty_months }} Bulan</strong>
                                    </div>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="price-section mb-3 p-3 bg-light rounded">
                                <small class="text-muted d-block">Harga Mulai</small>
                                <h4 class="mb-0 text-primary">Rp {{ number_format($service->price, 0, ',', '.') }}</h4>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.bookings.create', ['service_id' => $service->id]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-calendar-alt"></i> Pesan Layanan
                                </a>
                                <a href="#" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#serviceModal{{ $service->id }}">
                                    <i class="fas fa-info-circle"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Detail Modal -->
                <div class="modal fade" id="serviceModal{{ $service->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title">{{ $service->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Deskripsi Lengkap</h6>
                                <p>{{ $service->description }}</p>

                                <h6 class="mt-3">Spesifikasi</h6>
                                <ul>
                                    <li>Durasi: {{ $service->duration_minutes }} Menit</li>
                                    <li>Kategori: {{ $service->category }}</li>
                                    <li>Harga: Rp {{ number_format($service->price, 0, ',', '.') }}</li>
                                    @if($service->warranty_months)
                                        <li>Garansi: {{ $service->warranty_months }} Bulan</li>
                                    @endif
                                </ul>

                                @if($service->included_items)
                                    <h6 class="mt-3">Yang Termasuk</h6>
                                    <ul>
                                        @foreach(explode(',', $service->included_items) as $item)
                                            <li>{{ trim($item) }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <a href="{{ route('customer.bookings.create', ['service_id' => $service->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-calendar-alt"></i> Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $services->links() }}
        </div>
    @else
        <div class="card shadow-sm border-0 text-center py-5">
            <i class="fas fa-search" style="font-size: 48px; color: #ccc;"></i>
            <h5 class="mt-3 text-muted">Layanan Tidak Ditemukan</h5>
            <p class="text-muted">Coba ubah filter pencarian Anda</p>
        </div>
    @endif
</div>

<style>
    .page-header {
        padding: 20px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .service-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }

    .price-section {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border: 2px solid #e0e0e0;
    }
</style>
@endsection
