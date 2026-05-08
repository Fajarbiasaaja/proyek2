@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
    <div class="page-title">
        <i class="bi bi-people"></i>
        <h1>Detail Pelanggan - {{ $customer->name }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Informasi Pelanggan
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Nama:</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>Telepon:</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>Alamat:</th>
                            <td>{{ $customer->address }}</td>
                        </tr>
                        <tr>
                            <th>Kota:</th>
                            <td>{{ $customer->city ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kode Pos:</th>
                            <td>{{ $customer->postal_code ?? '-' }}</td>
                        </tr>
                    </table>
                    <div class="d-flex gap-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calendar-check"></i> Riwayat Pemesanan
                </div>
                <div class="card-body">
                    @if($bookings->isEmpty())
                        <p class="text-muted mb-0">Tidak ada pemesanan.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
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
        </div>
    </div>
@endsection
