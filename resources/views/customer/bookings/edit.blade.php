@extends('layouts.app')

@section('title', 'Edit Pemesanan')

@section('content')
    <div class="page-title">
        <i class="bi bi-pencil"></i>
        <h1>Edit Pemesanan #{{ $booking->id }}</h1>
    </div>

    @if($booking->status !== 'pending')
        <div class="alert alert-warning mb-3" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Perhatian!</strong> Pemesanan hanya bisa diedit ketika status masih <strong>Pending</strong>. Saat ini status pemesanan Anda adalah <strong>{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</strong>.
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-form-check"></i> Form Edit Pemesanan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.bookings.update', $booking) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Layanan Selection -->
                            <div class="mb-4">
                                <label for="service_id" class="form-label"><strong>Pilih Layanan <span class="text-danger">*</span></strong></label>
                                <select class="form-control @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required onchange="updateServiceInfo()">
                                    <option value="">-- Pilih Layanan --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-description="{{ $service->description }}" @if($booking->service_id == $service->id) selected @endif>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="serviceInfo" class="alert alert-info mt-3">
                                    <h6 id="serviceName"></h6>
                                    <p id="serviceDescription" class="mb-2"></p>
                                    <p class="mb-0"><strong>Harga: </strong><span id="servicePrice"></span></p>
                                </div>
                            </div>

                            <!-- Teknisi Selection (Optional) -->
                            <div class="mb-4">
                                <label for="technician_id" class="form-label">Pilih Teknisi (Opsional)</label>
                                <select class="form-control @error('technician_id') is-invalid @enderror" id="technician_id" name="technician_id">
                                    <option value="">-- Biarkan Admin Menugaskan --</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" @if($booking->technician_id == $technician->id) selected @endif>
                                            {{ $technician->name }} ({{ $technician->specialization }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('technician_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">Jika tidak memilih, admin akan menugaskan teknisi yang tersedia</small>
                            </div>

                            <!-- Schedule Date & Time -->
                            <div class="mb-4">
                                <label for="scheduled_date" class="form-label"><strong>Tanggal & Waktu Jadwal <span class="text-danger">*</span></strong></label>
                                <input type="datetime-local" class="form-control @error('scheduled_date') is-invalid @enderror" id="scheduled_date" name="scheduled_date" value="{{ $booking->scheduled_date->format('Y-m-d\TH:i') }}" required>
                                @error('scheduled_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">Pilih tanggal dan waktu kapan Anda ingin layanan dilakukan</small>
                            </div>

                            <!-- Notes -->
                            <div class="mb-4">
                                <label for="notes" class="form-label">Catatan atau Permintaan Khusus (Opsional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Contoh: AC terdapat bocor, bising saat dihidupkan, dll...">{{ $booking->notes }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">Berikan informasi tambahan untuk membantu teknisi mempersiapkan perlengkapan</small>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('customer.bookings.index') }}" class="btn btn-light">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-circle"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Detail Pemesanan</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">
                            <strong>ID Pemesanan:</strong><br>
                            #{{ $booking->id }}
                        </p>
                        <hr>
                        <p class="small mb-2">
                            <strong>Status:</strong><br>
                            <span class="badge bg-warning text-dark">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                        </p>
                        <hr>
                        <p class="small mb-0">
                            <strong>Harga Saat Ini:</strong><br>
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <p class="small mb-0">
                        <strong>📝 Catatan:</strong> Anda dapat mengubah layanan, teknisi, tanggal, dan waktu pemesanan selama status masih <strong>Pending</strong>.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <script>
        function updateServiceInfo() {
            const select = document.getElementById('service_id');
            const selected = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('serviceInfo');

            if (select.value) {
                document.getElementById('serviceName').textContent = selected.text;
                document.getElementById('serviceDescription').textContent = selected.getAttribute('data-description');
                document.getElementById('servicePrice').textContent = 'Rp ' + parseInt(selected.getAttribute('data-price')).toLocaleString('id-ID');
                infoDiv.style.display = 'block';
            }
        }

        // Update on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateServiceInfo();

            // Set minimum date to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('scheduled_date').min = now.toISOString().slice(0, 16);
        });
    </script>
@endsection
