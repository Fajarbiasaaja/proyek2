@extends('layouts.app')

@section('title', 'Daftar Pemesanan Saya')

@section('content')
    <div class="page-title">
        <i class="bi bi-calendar-check"></i>
        <h1>Pemesanan Saya</h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <h5>Total Pemesanan</h5>
                <div class="number">{{ $bookings->total() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card success">
                <h5>Menunggu</h5>
                <div class="number">{{ $bookings->where('status', 'pending')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h5>Terkonfirmasi</h5>
                <div class="number">{{ $bookings->where('status', 'confirmed')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card success">
                <h5>Selesai</h5>
                <div class="number">{{ $bookings->where('status', 'completed')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Daftar Pemesanan</h5>
            <a href="{{ route('customer.bookings.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Buat Pemesanan Baru
            </a>
        </div>

        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Layanan</th>
                            <th style="width: 18%;">Tanggal Jadwal</th>
                            <th style="width: 15%;">Teknisi</th>
                            <th style="width: 12%;">Harga</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 23%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->service->name }}</strong>
                                </td>
                                <td>
                                    {{ $booking->scheduled_date->format('d/m/Y H:i') }}<br>
                                    <small class="text-muted">{{ $booking->scheduled_date->diffForHumans() }}</small>
                                </td>
                                <td>
                                    {{ $booking->technician?->name ?? '(Belum ditugaskan)' }}
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="badge @switch($booking->status)
                                        @case('pending') bg-warning text-dark @break
                                        @case('confirmed') bg-info @break
                                        @case('in_progress') bg-primary @break
                                        @case('completed') bg-success @break
                                        @case('cancelled') bg-danger @break
                                    @endswitch">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-outline-primary" title="Lihat Detail">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                        
                                        @if($booking->status === 'pending')
                                            <a href="{{ route('customer.bookings.edit', $booking) }}" class="btn btn-outline-warning" title="Edit Pemesanan">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        @endif

                                        @if(in_array($booking->status, ['pending', 'confirmed', 'in_progress']))
                                            <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Batalkan pemesanan ini?')" title="Batalkan Pemesanan">
                                                    <i class="bi bi-x-circle"></i> Batalkan
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

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="card-footer" style="background: white;">
                    {{ $bookings->links() }}
                </div>
            @endif
        @else
            <div style="padding: 60px 20px; text-align: center;">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666; margin-top: 20px;">
                    <strong>Anda belum membuat pemesanan.</strong>
                </p>
                <p class="text-muted mb-3">Buat pemesanan baru untuk mulai menggunakan layanan kami</p>
                <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Buat Pemesanan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Info Card -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle"></i> Status Pemesanan</h6>
                    <ul class="small mb-0">
                        <li><strong>Pending:</strong> Menunggu konfirmasi dari admin</li>
                        <li><strong>Confirmed:</strong> Sudah dikonfirmasi, menunggu jadwal</li>
                        <li><strong>In Progress:</strong> Teknisi sedang melayani</li>
                        <li><strong>Completed:</strong> Layanan selesai</li>
                        <li><strong>Cancelled:</strong> Pemesanan dibatalkan</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-check-circle"></i> Panduan</h6>
                    <ul class="small mb-0">
                        <li>Klik <strong>"Buat Pemesanan"</strong> untuk membuat layanan baru</li>
                        <li>Hanya bisa edit pemesanan dengan status <strong>Pending</strong></li>
                        <li>Setelah admin konfirmasi, Anda akan dapat invoice untuk pembayaran</li>
                        <li>Pantau status pemesanan Anda di halaman ini</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
