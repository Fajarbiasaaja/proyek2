@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
    <div class="page-title">
        <i class="bi bi-receipt"></i>
        <h1>Edit Invoice {{ $invoice->invoice_number }}</h1>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <i class="bi bi-pencil"></i> Form Edit Invoice
        </div>
        <div class="card-body">
            <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="draft" @if(old('status', $invoice->status) === 'draft') selected @endif>Draft</option>
                        <option value="issued" @if(old('status', $invoice->status) === 'issued') selected @endif>Diterbitkan</option>
                        <option value="paid" @if(old('status', $invoice->status) === 'paid') selected @endif>Dibayar</option>
                        <option value="overdue" @if(old('status', $invoice->status) === 'overdue') selected @endif>Jatuh Tempo</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="paid_date" class="form-label">Tanggal Pembayaran</label>
                    <input type="date" class="form-control @error('paid_date') is-invalid @enderror" id="paid_date" name="paid_date" value="{{ old('paid_date', $invoice->paid_date?->format('Y-m-d')) }}">
                    @error('paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Ringkasan Invoice</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Nomor Invoice:</th>
                                <td>{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <th>Pelanggan:</th>
                                <td>{{ $invoice->booking->customer->name }}</td>
                            </tr>
                            <tr>
                                <th>Layanan:</th>
                                <td>{{ $invoice->booking->service->name }}</td>
                            </tr>
                            <tr>
                                <th>Subtotal:</th>
                                <td>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Pajak:</th>
                                <td>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th><strong>Total:</strong></th>
                                <td><strong>Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Perbarui
                    </button>
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
