@extends('layouts.app')

@section('title', 'Daftar Invoice')

@section('content')
    <div class="page-title">
        <i class="bi bi-receipt"></i>
        <h1>Daftar Invoice</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-receipt"></i> Invoice</span>
        </div>
        <div class="card-body">
            @if($invoices->isEmpty())
                <p class="text-muted mb-0">Tidak ada invoice.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nomor Invoice</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal Pembuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                    <td>{{ $invoice->booking->customer->name }}</td>
                                    <td>{{ $invoice->booking->service->name }}</td>
                                    <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge @if($invoice->status === 'paid') bg-success @elseif($invoice->status === 'overdue') bg-danger @elseif($invoice->status === 'issued') bg-primary @else bg-secondary @endif">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $invoices->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
