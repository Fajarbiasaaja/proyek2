@extends('layouts.app')

@section('title', 'Buat Pemesanan')

@section('content')
    <div class="page-title">
        <i class="bi bi-calendar-check"></i>
        <h1>Buat Pemesanan Baru</h1>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <i class="bi bi-plus-circle"></i> Form Buat Pemesanan
        </div>
        <div class="card-body">
            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Pelanggan *</label>
                    <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                        <option value="">Pilih Pelanggan</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @if(old('customer_id') == $customer->id) selected @endif>{{ $customer->name }} ({{ $customer->phone }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="service_id" class="form-label">Layanan *</label>
                    <select class="form-control @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                        <option value="">Pilih Layanan</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @if(old('service_id') == $service->id) selected @endif>{{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                    @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="technician_id" class="form-label">Teknisi</label>
                    <select class="form-control @error('technician_id') is-invalid @enderror" id="technician_id" name="technician_id">
                        <option value="">Pilih Teknisi (Opsional)</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" @if(old('technician_id') == $technician->id) selected @endif>{{ $technician->name }} ({{ $technician->specialization }})</option>
                        @endforeach
                    </select>
                    @error('technician_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="scheduled_date" class="form-label">Tanggal & Waktu Jadwal *</label>
                    <input type="datetime-local" class="form-control @error('scheduled_date') is-invalid @enderror" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}" required>
                    @error('scheduled_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="pending" @if(old('status') === 'pending') selected @endif>Menunggu</option>
                        <option value="confirmed" @if(old('status') === 'confirmed') selected @endif>Dikonfirmasi</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
