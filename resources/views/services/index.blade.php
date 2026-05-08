@extends('layouts.app')

@section('title', 'Daftar Layanan')

@section('content')
    <style>
        .service-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .service-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-bottom: 4px solid #667eea;
        }

        .service-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
        }

        .service-body {
            padding: 20px;
        }

        .service-detail {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #555;
            font-size: 14px;
        }

        .service-detail i {
            width: 20px;
            margin-right: 10px;
            color: #667eea;
            font-size: 16px;
        }

        .service-description {
            color: #777;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price-badge {
            display: inline-block;
            background: #e7f3ff;
            color: #0066cc;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin-right: 10px;
        }

        .duration-badge {
            display: inline-block;
            background: #f0f0f0;
            color: #333;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
        }

        .service-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 8px;
        }

        .service-actions .btn {
            flex: 1;
            font-size: 12px;
            padding: 8px;
            border-radius: 6px;
        }

        .page-title {
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }

        .add-btn-container {
            margin-bottom: 20px;
        }
    </style>

    <div class="page-title d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-wrench" style="font-size: 28px; color: #667eea;"></i>
            <h1 class="d-inline-block ms-2">Daftar Layanan</h1>
        </div>
        <a href="{{ route('services.create') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle"></i> Tambah Layanan Baru
        </a>
    </div>

    @if($services->isEmpty())
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-inbox" style="font-size: 48px; color: #0066cc; opacity: 0.5;"></i>
            <p class="mt-3 mb-0">Belum ada layanan. <a href="{{ route('services.create') }}">Buat layanan baru</a></p>
        </div>
    @else
        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card">
                        <div class="service-header">
                            <h5>{{ $service->name }}</h5>
                            <small class="opacity-75">Rekayasa Perangkat Lunak</small>
                        </div>

                        <div class="service-body">
                            <!-- Deskripsi -->
                            @if($service->description)
                                <p class="service-description">{{ $service->description }}</p>
                            @endif

                            <!-- Detail Service -->
                            <div class="service-detail">
                                <i class="bi bi-cash-coin"></i>
                                <strong>Rp {{ number_format($service->price, 0, ',', '.') }}</strong>
                            </div>

                            <div class="service-detail">
                                <i class="bi bi-clock-history"></i>
                                <span>{{ $service->duration_minutes }} Menit</span>
                            </div>

                            <div class="service-detail">
                                <i class="bi bi-calendar-event"></i>
                                <span>{{ $service->bookings_count }} Pemesanan</span>
                            </div>

                            <!-- Progress/Status -->
                            <div style="margin-top: 15px;">
                                <small class="text-muted">Status</small>
                                <div class="progress" style="height: 8px; margin-top: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                        style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">85% Tersedia</small>
                            </div>

                            <!-- Aksi -->
                            <div class="service-actions">
                                <a href="{{ route('services.show', $service) }}" 
                                    class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    <a href="{{ route('services.edit', $service) }}" 
                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('services.destroy', $service) }}" method="POST" 
                                        style="flex: 1; display: flex;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" 
                                            onclick="return confirm('Hapus layanan ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary" style="flex: 1;"
                                        data-bs-toggle="modal" data-bs-target="#createBookingModal" title="Pesan">
                                        <i class="bi bi-calendar-plus"></i> Pesan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($services->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $services->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
@endsection
