@extends('layouts.app')

@section('title', 'Manajemen Transaksi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>Manajemen Transaksi
                </h1>
                <small class="text-muted">Kelola pesanan dan pembayaran pelanggan</small>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" role="tab">
                <i class="fas fa-tasks me-2"></i>Pesanan (Bookings)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" role="tab">
                <i class="fas fa-credit-card me-2"></i>Pembayaran
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" role="tab">
                <i class="fas fa-file-invoice me-2"></i>Invoice
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Bookings Tab -->
        <div class="tab-pane fade show active" id="bookings" role="tabpanel">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau ID pesanan..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($bookings && $bookings->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Pelanggan</th>
                                    <th>Layanan</th>
                                    <th>Teknisi</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $booking->customer->user->name }}</td>
                                        <td>{{ $booking->service->name }}</td>
                                        <td>
                                            @if($booking->technician)
                                                <span class="badge bg-info">{{ $booking->technician->user->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">Belum Ditugaskan</span>
                                            @endif
                                        </td>
                                        <td>{{ $booking->scheduled_date->format('d M Y, H:i') }}</td>
                                        <td>
                                            @switch($booking->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-info">Terkonfirmasi</span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="badge bg-primary">Sedang Dikerjakan</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Selesai</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Dibatalkan</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="card shadow-sm border-0 text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Pesanan</h5>
                </div>
            @endif
        </div>

        <!-- Payments Tab -->
        <div class="tab-pane fade" id="payments" role="tabpanel">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari ID pembayaran..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($payments && $payments->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Pembayaran</th>
                                    <th>Pelanggan</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>#{{ $payment->id }}</td>
                                        <td>{{ $payment->invoice->booking->customer->user->name }}</td>
                                        <td><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $payment->payment_method ?? 'Bank Transfer' }}</span>
                                        </td>
                                        <td>
                                            @if($payment->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($payment->status === 'approved')
                                                <span class="badge bg-info">Disetujui</span>
                                            @elseif($payment->status === 'confirmed')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->status === 'pending')
                                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $payment->id }}">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="card shadow-sm border-0 text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Pembayaran</h5>
                </div>
            @endif
        </div>

        <!-- Invoices Tab -->
        <div class="tab-pane fade" id="invoices" role="tabpanel">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari nomor invoice..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Jatuh Tempo</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($invoices && $invoices->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Pelanggan</th>
                                    <th>Layanan</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td><strong>#{{ $invoice->invoice_number }}</strong></td>
                                        <td>{{ $invoice->booking->customer->user->name }}</td>
                                        <td>{{ $invoice->booking->service->name }}</td>
                                        <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                        <td>
                                            @if($invoice->status === 'paid')
                                                <span class="badge bg-success">Lunas</span>
                                            @else
                                                <span class="badge bg-danger">Belum Dibayar</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                                            @if($invoice->status === 'unpaid' && $invoice->due_date && $invoice->due_date->isPast())
                                                <br><small class="text-danger">OVERDUE</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="card shadow-sm border-0 text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Invoice</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .page-header {
        padding: 20px 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    table tr:hover {
        background-color: #f8f9fa;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
    }
    
    .nav-tabs .nav-link.active {
        color: #0066cc;
        border-bottom: 3px solid #0066cc;
        background: none;
    }
</style>
@endsection
