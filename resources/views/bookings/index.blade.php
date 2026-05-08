@extends('layouts.app')

@section('title', 'Daftar Pemesanan')

@section('content')
    <div class="page-title">
        <i class="bi bi-calendar-check"></i>
        <h1>Daftar Pemesanan</h1>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-check"></i> Pemesanan</span>
            <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Buat Pemesanan
            </a>
        </div>
        <div class="card-body">
            @if($bookings->isEmpty())
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
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->customer->name }}</td>
                                    <td>{{ $booking->service->name }}</td>
                                    <td>{{ $booking->technician?->name ?? '-' }}</td>
                                    <td>{{ $booking->scheduled_date->format('d/m/Y H:i') }}</td>
                                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
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
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $bookings->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
