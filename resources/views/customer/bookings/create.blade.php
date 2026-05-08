@extends('layouts.app')

@section('title', 'Buat Pemesanan')

@section('content')
    <div class="page-title">
        <i class="bi bi-plus-circle"></i>
        <h1>Buat Pemesanan Baru</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-form-check"></i> Form Pemesanan Layanan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.bookings.store') }}" method="POST">
                        @csrf

                        <!-- Layanan Selection -->
                        <div class="mb-4">
                            <label for="service_id" class="form-label"><strong>Pilih Layanan <span class="text-danger">*</span></strong></label>
                            <select class="form-control @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required onchange="updateServiceInfo()">
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-description="{{ $service->description }}">
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="serviceInfo" class="alert alert-info mt-3" style="display: none;">
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
                                    <option value="{{ $technician->id }}" @if(old('technician_id') == $technician->id) selected @endif>
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
                            <input type="datetime-local" class="form-control @error('scheduled_date') is-invalid @enderror" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}" required>
                            @error('scheduled_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">Pilih tanggal dan waktu kapan Anda ingin layanan dilakukan</small>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan atau Permintaan Khusus (Opsional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Contoh: AC terdapat bocor, bising saat dihidupkan, dll...">{{ old('notes') }}</textarea>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Buat Pemesanan
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
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Penting</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <strong>Jadwal Layanan:</strong><br>
                        Pastikan memilih tanggal dan waktu sesuai ketersediaan Anda. Teknisi kami akan datang tepat waktu.
                    </p>
                    <hr>
                    <p class="small mb-2">
                        <strong>Catatan Penting:</strong><br>
                        Semakin detail catatan Anda, semakin baik teknisi kami dapat mempersiapkan diri.
                    </p>
                    <hr>
                    <p class="small mb-0">
                        <strong>Proses Pemesanan:</strong><br>
                        1. Buat pemesanan (status: Pending)<br>
                        2. Admin konfirmasi (status: Confirmed)<br>
                        3. Invoice dikirim untuk pembayaran<br>
                        4. Teknisi datang sesuai jadwal<br>
                        5. Pembayaran diselesaikan
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-question-circle"></i> Pertanyaan?</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-0">Jika ada pertanyaan, hubungi admin melalui dashboard atau whatsapp kami.</p>
                </div>
            </div>
        </div>
    </div>

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
            } else {
                infoDiv.style.display = 'none';
            }
        }

        // Update on page load if value exists
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('service_id').value) {
                updateServiceInfo();
            }

            // Set minimum date to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('scheduled_date').min = now.toISOString().slice(0, 16);
        });
    </script>
@endsection
