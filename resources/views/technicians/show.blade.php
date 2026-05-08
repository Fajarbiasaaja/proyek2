@extends('layouts.app')

@section('title', 'Detail Teknisi')

@section('content')
    <div class="page-title">
        <i class="bi bi-tools"></i>
        <h1>Detail Teknisi - {{ $technician->name }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Informasi Teknisi
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Nama:</th>
                            <td>{{ $technician->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $technician->email }}</td>
                        </tr>
                        <tr>
                            <th>Telepon:</th>
                            <td>{{ $technician->phone }}</td>
                        </tr>
                        <tr>
                            <th>Spesialisasi:</th>
                            <td>{{ $technician->specialization }}</td>
                        </tr>
                        <tr>
                            <th>Alamat:</th>
                            <td>{{ $technician->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge @if($technician->status === 'available') bg-success @elseif($technician->status === 'busy') bg-warning @else bg-danger @endif">
                                    {{ ucfirst($technician->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                    <div class="d-flex gap-2">
                        <a href="{{ route('technicians.edit', $technician) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('technicians.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calendar-check"></i> Riwayat Pekerjaan
                </div>
                <div class="card-body">
                    @if($bookings->isEmpty())
                        <p class="text-muted mb-0">Tidak ada pekerjaan.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Pelanggan</th>
                                        <th>Layanan</th>
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
