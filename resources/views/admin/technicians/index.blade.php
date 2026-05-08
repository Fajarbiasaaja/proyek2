@extends('layouts.app')

@section('title', 'Manajemen Penyedia Jasa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">
                    <i class="fas fa-tools me-2"></i>Manajemen Penyedia Jasa
                </h1>
                <small class="text-muted">Kelola data teknisi dan penyedia layanan</small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('technicians.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Teknisi
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Teknisi</h6>
                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Sedang Aktif</h6>
                    <h3 class="mb-0 text-success">{{ $stats['active'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Rating Rata-rata</h6>
                    <h3 class="mb-0">{{ number_format($stats['avg_rating'] ?? 0, 1) }}/5</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Pesanan Selesai</h6>
                    <h3 class="mb-0">{{ $stats['completed_jobs'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="rating" class="form-select">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>★★★★★ (5.0)</option>
                        <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>★★★★☆ (4.0+)</option>
                        <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>★★★☆☆ (3.0+)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Technicians Table -->
    @if($technicians && $technicians->count() > 0)
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Teknisi</th>
                            <th>Email</th>
                            <th>Keahlian</th>
                            <th>Pengalaman</th>
                            <th>Rating</th>
                            <th>Pesanan Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $tech)
                            <tr>
                                <td>
                                    <strong>{{ $tech->user->name }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $tech->user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $tech->specialization ?? 'General' }}</span>
                                </td>
                                <td>
                                    {{ $tech->experience_years ?? 0 }} tahun
                                </td>
                                <td>
                                    <div class="text-warning">
                                        @for($i = 0; $i < 5; $i++)
                                            @if($i < floor($tech->average_rating ?? 0))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                        <small class="text-muted ms-1">{{ number_format($tech->average_rating ?? 0, 1) }}/5</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $tech->completed_jobs ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($tech->user->role === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('technicians.show', $tech) }}" class="btn btn-outline-primary" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('technicians.edit', $tech) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('technicians.destroy', $tech) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $technicians->links() }}
        </div>
    @else
        <div class="card shadow-sm border-0 text-center py-5">
            <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
            <h5 class="mt-3 text-muted">Tidak Ada Teknisi</h5>
        </div>
    @endif
</div>

<style>
    .page-header {
        padding: 20px 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    table tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
