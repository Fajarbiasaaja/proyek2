@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
    <div class="page-title">
        <i class="bi bi-calendar-check"></i>
        <h1>Detail Pemesanan #{{ $booking->id }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Informasi Pemesanan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th>Pelanggan:</th>
                                    <td><a href="{{ route('customers.show', $booking->customer) }}">{{ $booking->customer->name }}</a></td>
                                </tr>
                                <tr>
                                    <th>Telepon:</th>
                                    <td>{{ $booking->customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Layanan:</th>
                                    <td><a href="{{ route('services.show', $booking->service) }}">{{ $booking->service->name }}</a></td>
                                </tr>
                                <tr>
                                    <th>Harga:</th>
                                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th>Teknisi:</th>
                                    <td>{{ $booking->technician ? $booking->technician->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Jadwal:</th>
                                    <td>{{ $booking->scheduled_date->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
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
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="mt-3">
                        <strong>Catatan:</strong>
                        <p class="text-muted">{{ $booking->notes }}</p>
                    </div>
                    @endif

                    @if($booking->completion_notes)
                    <div class="mt-3">
                        <strong>Catatan Penyelesaian:</strong>
                        <p class="text-muted">{{ $booking->completion_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($booking->invoice)
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-receipt"></i> Invoice
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Nomor Invoice:</th>
                            <td><a href="{{ route('invoices.show', $booking->invoice) }}">{{ $booking->invoice->invoice_number }}</a></td>
                        </tr>
                        <tr>
                            <th>Subtotal:</th>
                            <td>Rp {{ number_format($booking->invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Pajak:</th>
                            <td>Rp {{ number_format($booking->invoice->tax, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th><strong>Total:</strong></th>
                            <td><strong>Rp {{ number_format($booking->invoice->total, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge @if($booking->invoice->status === 'paid') bg-success @elseif($booking->invoice->status === 'overdue') bg-danger @else bg-warning @endif">
                                    {{ ucfirst($booking->invoice->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if(auth()->user()->role === 'admin')
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-tools"></i> Aksi
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    
                    @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                    <form action="{{ route('bookings.markAsCompleted', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Tandai pemesanan sebagai selesai?')">
                            <i class="bi bi-check-circle"></i> Tandai Selesai
                        </button>
                    </form>
                    @endif

                    @if($booking->status !== 'cancelled')
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Batalkan pemesanan ini?')">
                            <i class="bi bi-x-circle"></i> Batalkan
                        </button>
                    </form>
                    @endif

                    @if($booking->invoice)
                    <a href="{{ route('invoices.show', $booking->invoice) }}" class="btn btn-info">
                        <i class="bi bi-receipt"></i> Lihat Invoice
                    </a>
                    @endif

                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <p class="text-muted mb-3"><strong>Anda dapat melihat detail pemesanan Anda</strong></p>
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
