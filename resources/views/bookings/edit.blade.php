@extends('layouts.app')

@section('title', 'Edit Pemesanan')

@section('content')
    <div class="page-title">
        <i class="bi bi-calendar-check"></i>
        <h1>Edit Pemesanan</h1>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <i class="bi bi-pencil"></i> Form Edit Pemesanan
        </div>
        <div class="card-body">
            <form action="{{ route('bookings.update', $booking) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Pelanggan *</label>
                    <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @if(old('customer_id', $booking->customer_id) == $customer->id) selected @endif>{{ $customer->name }} ({{ $customer->phone }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="service_id" class="form-label">Layanan *</label>
                    <select class="form-control @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @if(old('service_id', $booking->service_id) == $service->id) selected @endif>{{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                    @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="technician_id" class="form-label">Teknisi</label>
                    <select class="form-control @error('technician_id') is-invalid @enderror" id="technician_id" name="technician_id">
                        <option value="">Pilih Teknisi</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" @if(old('technician_id', $booking->technician_id) == $technician->id) selected @endif>{{ $technician->name }} ({{ $technician->specialization }})</option>
                        @endforeach
                    </select>
                    @error('technician_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="scheduled_date" class="form-label">Tanggal & Waktu Jadwal *</label>
                    <input type="datetime-local" class="form-control @error('scheduled_date') is-invalid @enderror" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date', $booking->scheduled_date->format('Y-m-d\TH:i')) }}" required>
                    @error('scheduled_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $booking->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="pending" @if(old('status', $booking->status) === 'pending') selected @endif>Menunggu</option>
                        <option value="confirmed" @if(old('status', $booking->status) === 'confirmed') selected @endif>Dikonfirmasi</option>
                        <option value="in_progress" @if(old('status', $booking->status) === 'in_progress') selected @endif>Sedang Berlangsung</option>
                        <option value="completed" @if(old('status', $booking->status) === 'completed') selected @endif>Selesai</option>
                        <option value="cancelled" @if(old('status', $booking->status) === 'cancelled') selected @endif>Dibatalkan</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if($booking->status === 'completed' || old('status') === 'completed')
                <div class="mb-3">
                    <label for="completion_notes" class="form-label">Catatan Penyelesaian</label>
                    <textarea class="form-control @error('completion_notes') is-invalid @enderror" id="completion_notes" name="completion_notes" rows="2">{{ old('completion_notes', $booking->completion_notes) }}</textarea>
                    @error('completion_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @endif

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Perbarui
                    </button>
                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
