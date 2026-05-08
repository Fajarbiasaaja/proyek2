@extends('layouts.app')

@section('title', 'Manajemen Data Pelanggan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>Manajemen Pelanggan
                </h1>
                <small class="text-muted">Kelola data dan informasi pelanggan sistem</small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Pelanggan
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Pelanggan</h6>
                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Aktif Bulan Ini</h6>
                    <h3 class="mb-0 text-success">{{ $stats['active_this_month'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Pesanan</h6>
                    <h3 class="mb-0">{{ $stats['total_bookings'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Pendapatan</h6>
                    <h3 class="mb-0">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</h3>
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
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nama</option>
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

    <!-- Customers Table -->
    @if($customers && $customers->count() > 0)
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Pelanggan</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th>Total Pesanan</th>
                            <th>Total Pengeluaran</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <strong>{{ $customer->user->name }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $customer->user->email }}</small>
                                </td>
                                <td>
                                    {{ $customer->phone ?? '-' }}
                                </td>
                                <td>
                                    <small>{{ substr($customer->address ?? '-', 0, 30) }}...</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $customer->bookings_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $customer->user->created_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline;">
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
            {{ $customers->links() }}
        </div>
    @else
        <div class="card shadow-sm border-0 text-center py-5">
            <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
            <h5 class="mt-3 text-muted">Tidak Ada Pelanggan</h5>
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
