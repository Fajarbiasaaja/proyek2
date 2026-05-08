@extends('layouts.app')

@section('title', 'Dashboard Pelanggan')

@section('content')
    <div class="page-title">
        <i class="bi bi-speedometer2"></i>
        <h1>Dashboard Pelanggan</h1>
    </div>

    <!-- Welcome Card -->
    <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white;">
        <h5 class="mb-1">Selamat datang, {{ $customer->name }}!</h5>
        <p class="mb-0">Kelola pemesanan servis AC Anda dengan mudah melalui dashboard ini.</p>
    </div>

    <!-- NOTIFIKASI INVOICE OVERDUE/BELUM DIBAYAR -->
    
    <!-- Alert Overdue Invoices - DANGER -->
    @if($overdueCount > 0)
        <div class="alert alert-danger border-0 mb-3 shadow-sm" style="border-left: 5px solid #dc3545;">
            <div class="d-flex align-items-start gap-3">
                <div style="font-size: 1.5rem; margin-top: 2px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div style="flex: 1;">
                    <h5 class="alert-heading mb-2">
                        <i class="bi bi-alarm"></i> Invoice Jatuh Tempo!
                    </h5>
                    <p class="mb-2">
                        Anda memiliki <strong>{{ $overdueCount }} invoice</strong> yang <strong>JATUH TEMPO</strong> dan harus dibayar segera:
                    </p>
                    @foreach($overdueInvoices as $invoice)
                        <div class="alert alert-light mb-2 border-start border-danger" style="padding: 10px 15px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $invoice->invoice_number }}</strong><br>
                                    <small class="text-muted">{{ $invoice->booking->service->name }}</small><br>
                                    <small class="text-danger">
                                        <i class="bi bi-calendar-x"></i>
                                        Jatuh tempo: {{ $invoice->due_date->format('d/m/Y') }}
                                        ({{ $invoice->due_date->diffForHumans() }})
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="text-danger fw-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
                                    <a href="{{ route('customer.invoice.show', $invoice) }}" class="btn btn-sm btn-danger mt-2">
                                        <i class="bi bi-credit-card"></i> Bayar Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Alert Upcoming Due Invoices - WARNING -->
    @if($upcomingCount > 0)
        <div class="alert alert-warning border-0 mb-3 shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="d-flex align-items-start gap-3">
                <div style="font-size: 1.5rem; margin-top: 2px;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div style="flex: 1;">
                    <h5 class="alert-heading mb-2">
                        <i class="bi bi-hourglass-split"></i> Invoice Akan Jatuh Tempo
                    </h5>
                    <p class="mb-2">
                        Anda memiliki <strong>{{ $upcomingCount }} invoice</strong> yang akan jatuh tempo dalam 3 hari ke depan:
                    </p>
                    @foreach($upcomingDueInvoices as $invoice)
                        <div class="alert alert-light mb-2 border-start border-warning" style="padding: 10px 15px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $invoice->invoice_number }}</strong><br>
                                    <small class="text-muted">{{ $invoice->booking->service->name }}</small><br>
                                    <small class="text-warning">
                                        <i class="bi bi-calendar"></i>
                                        Jatuh tempo: {{ $invoice->due_date->format('d/m/Y') }}
                                        ({{ $invoice->due_date->diffForHumans() }})
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="text-warning fw-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
                                    <a href="{{ route('customer.invoice.show', $invoice) }}" class="btn btn-sm btn-warning mt-2">
                                        <i class="bi bi-credit-card"></i> Bayar Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- AUTO SLIDER CAROUSEL SERVICE -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <div style="position: relative; width: 100%; height: 350px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            
            <!-- Carousel Container -->
            <div id="serviceCarousel" style="position: relative; height: 100%; overflow: hidden;">
                @php $services = \App\Models\Service::all(); @endphp
                @foreach($services as $index => $service)
                    <div class="carousel-item" data-index="{{ $index }}" style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 40px;
                        color: white;
                        opacity: {{ $index === 0 ? '1' : '0' }};
                        transition: opacity 1s ease-in-out;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    ">
                        <div style="flex: 1; max-width: 50%;">
                            <h2 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 15px;">
                                {{ $service->name }}
                            </h2>
                            <p style="font-size: 1.1rem; margin-bottom: 20px; line-height: 1.6;">
                                {{ $service->description }}
                            </p>
                            <div style="display: flex; gap: 20px; align-items: center;">
                                <div>
                                    <small style="opacity: 0.8;">Harga</small>
                                    <h4 style="color: #fff; font-weight: bold; margin: 5px 0;">
                                        Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </h4>
                                </div>
                                <div style="border-left: 2px solid rgba(255,255,255,0.3); padding-left: 20px;">
                                    <small style="opacity: 0.8;">Durasi</small>
                                    <h4 style="color: #fff; font-weight: bold; margin: 5px 0;">
                                        {{ $service->duration_minutes }} Menit
                                    </h4>
                                </div>
                            </div>
                            <button type="button" class="btn btn-light mt-4" style="font-weight: bold;" data-bs-toggle="modal" data-bs-target="#createBookingModal">
                                <i class="bi bi-arrow-right"></i> Pesan Sekarang
                            </button>
                        </div>
                        <div style="flex: 1; text-align: center; font-size: 150px; opacity: 0.3;">
                            <i class="bi bi-snow"></i>
                        </div>
                    </div>
                @endforeach

            <!-- Navigation Buttons -->
            <button id="prevBtn" onclick="changeSlide(-1)" style="
                position: absolute;
                left: 20px;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(255,255,255,0.3);
                border: none;
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 24px;
                z-index: 10;
                transition: background 0.3s;
            " onmouseover="this.style.background='rgba(255,255,255,0.5)'" onmouseout="this.style.background='rgba(255,255,255,0.3)'">
                <i class="bi bi-chevron-left"></i>
            </button>

            <button id="nextBtn" onclick="changeSlide(1)" style="
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(255,255,255,0.3);
                border: none;
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 24px;
                z-index: 10;
                transition: background 0.3s;
            " onmouseover="this.style.background='rgba(255,255,255,0.5)'" onmouseout="this.style.background='rgba(255,255,255,0.3)'">
                <i class="bi bi-chevron-right"></i>
            </button>

            <!-- Dots Indicators -->
            <div id="dotsContainer" style="
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 8px;
                z-index: 10;
            ">
                @foreach($services as $index => $service)
                    <span class="dot" data-index="{{ $index }}" style="
                        width: 12px;
                        height: 12px;
                        border-radius: 50%;
                        background: {{ $index === 0 ? 'rgba(255,255,255,1)' : 'rgba(255,255,255,0.5)' }};
                        cursor: pointer;
                        transition: background 0.3s;
                    " onclick="goToSlide({{ $index }})"></span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Auto Slider -->
    <script>
        let currentSlide = 0;
        let autoSlideInterval;
        const slides = document.querySelectorAll('.carousel-item');
        const totalSlides = slides.length;

        function updateSlide() {
            // Hide all slides
            slides.forEach(slide => {
                slide.style.opacity = '0';
            });

            // Update dots
            document.querySelectorAll('.dot').forEach((dot, index) => {
                dot.style.background = index === currentSlide ? 'rgba(255,255,255,1)' : 'rgba(255,255,255,0.5)';
            });

            // Show current slide
            if (slides[currentSlide]) {
                slides[currentSlide].style.opacity = '1';
            }
        }

        function changeSlide(direction) {
            currentSlide += direction;
            if (currentSlide >= totalSlides) currentSlide = 0;
            if (currentSlide < 0) currentSlide = totalSlides - 1;
            updateSlide();
            resetAutoSlide();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlide();
            resetAutoSlide();
        }

        function autoSlide() {
            changeSlide(1);
        }

        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(autoSlide, 5000); // Change slide every 5 seconds
        }

        // Initialize slider
        updateSlide();
        autoSlideInterval = setInterval(autoSlide, 5000);
    </script>

    <!-- Create Booking Modal -->
    @php
        $modalServices = \App\Models\Service::all();
        $modalTechnicians = \App\Models\Technician::where('status', '!=', 'inactive')->get();
    @endphp
    <div class="modal fade" id="createBookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Buat Pemesanan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customer.bookings.store') }}" method="POST" id="modalBookingForm">
                        @csrf
                        <div class="mb-3">
                            <label for="modal_service_id" class="form-label">Pilih Layanan *</label>
                            <select class="form-control" id="modal_service_id" name="service_id" required onchange="updateModalServiceInfo()">
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($modalServices as $s)
                                    <option value="{{ $s->id }}" data-price="{{ $s->price }}" data-desc="{{ $s->description }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal_technician_id" class="form-label">Pilih Teknisi (Opsional)</label>
                            <select class="form-control" id="modal_technician_id" name="technician_id">
                                <option value="">-- Biarkan Admin Menugaskan --</option>
                                @foreach($modalTechnicians as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->specialization }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal_scheduled_date" class="form-label">Tanggal & Waktu Jadwal *</label>
                            <input type="datetime-local" class="form-control" id="modal_scheduled_date" name="scheduled_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal_notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="modal_notes" name="notes" rows="3"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Buat Pemesanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateModalServiceInfo(){
            const sel = document.getElementById('modal_service_id');
            const opt = sel.options[sel.selectedIndex];
            // could show price/description if needed
        }

        document.addEventListener('DOMContentLoaded', function(){
            // Set min datetime for modal schedule input
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            const min = now.toISOString().slice(0,16);
            const i = document.getElementById('modal_scheduled_date');
            if(i) i.min = min;
        });
    </script>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <h5>Total Pemesanan</h5>
                <div class="number">{{ $totalBookings }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card success">
                <h5>Selesai</h5>
                <div class="number">{{ $completedBookings }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <h5>Menunggu</h5>
                <div class="number">{{ $pendingBookings }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <h5>Total Pengeluaran</h5>
                <div class="number" style="font-size: 1.5rem;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calendar-check"></i> Pemesanan Terakhir
                </div>
                <div class="card-body">
                    @if($recentBookings->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted mb-3">Anda belum membuat pemesanan.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBookingModal">
                                    <i class="bi bi-plus-circle"></i> Buat Pemesanan
                                </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Layanan</th>
                                        <th>Tanggal Jadwal</th>
                                        <th>Teknisi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td><strong>{{ $booking->service->name }}</strong></td>
                                            <td>{{ $booking->scheduled_date->format('d/m/Y H:i') }}</td>
                                            <td>{{ $booking->technician?->name ?? '-' }}</td>
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
                                            <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Unpaid Invoices -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-receipt"></i> Invoice Belum Dibayar
                </div>
                <div class="card-body">
                    @if($unpaidInvoices->isEmpty())
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Tidak ada invoice yang tertunggak.
                        </div>
                    @else
                        @foreach($unpaidInvoices as $invoice)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>{{ $invoice->invoice_number }}</strong>
                                    <span class="badge bg-warning">{{ ucfirst($invoice->status) }}</span>
                                </div>
                                <p class="mb-1 text-muted small">
                                    <i class="bi bi-wrench"></i> 
                                    {{ $invoice->booking->service->name }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-danger">
                                        Rp {{ number_format($invoice->total, 0, ',', '.') }}
                                    </strong>
                                    <a href="{{ route('customer.invoice.show', $invoice) }}" class="btn btn-sm btn-primary">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card mt-3 border-0 shadow-sm" style="border-radius: 10px;">
                <div class="card-header" style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white; border-radius: 10px 10px 0 0; border: none; padding: 12px 20px;">
                    <h5 class="mb-0" style="font-size: 16px;">
                        <i class="bi bi-person-circle"></i> Profil Anda
                    </h5>
                </div>
                <div class="card-body" style="padding: 20px;">
                    <div class="row g-3">
                        <!-- Nama -->
                        <div class="col-12 col-md-6">
                            <div style="text-align: center;">
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #e8f0ff 0%, #d4e3ff 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                    <i class="bi bi-person" style="font-size: 24px; color: #0066cc;"></i>
                                </div>
                                <small style="color: #6c757d; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; display: block;">Nama</small>
                                <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: 500; color: #212529;">{{ $customer->name }}</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-12 col-md-6">
                            <div style="text-align: center;">
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f0e8ff 0%, #e8d4ff 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                    <i class="bi bi-envelope" style="font-size: 24px; color: #6f42c1;"></i>
                                </div>
                                <small style="color: #6c757d; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; display: block;">Email</small>
                                <p style="margin: 4px 0 0 0; font-size: 12px; font-weight: 500; color: #212529; word-break: break-word;">{{ $customer->email }}</p>
                            </div>
                        </div>

                        <!-- Telepon -->
                        <div class="col-12 col-md-6">
                            <div style="text-align: center;">
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #fff8e8 0%, #ffe8c8 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                    <i class="bi bi-telephone" style="font-size: 24px; color: #ff9800;"></i>
                                </div>
                                <small style="color: #6c757d; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; display: block;">Telepon</small>
                                <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: 500; color: #212529;">{{ $customer->phone ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="col-12 col-md-6">
                            <div style="text-align: center;">
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #e8f5ff 0%, #c8e5ff 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                    <i class="bi bi-geo-alt" style="font-size: 24px; color: #0dcaf0;"></i>
                                </div>
                                <small style="color: #6c757d; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; display: block;">Alamat</small>
                                <p style="margin: 4px 0 0 0; font-size: 12px; font-weight: 500; color: #212529; line-height: 1.3;">{{ Str::limit($customer->address, 35) ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Button -->
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #dee2e6; text-align: center;">
                        <a href="{{ route('profile.editProfile') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white; border: none;">
            <h5 class="mb-0"><i class="bi bi-wrench"></i> Tersedia Layanan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse(\App\Models\Service::limit(6)->get() as $service)
                    <div class="col-md-4 mb-3">
                        <div class="card border h-100" style="transition: all 0.3s ease;">
                            <div class="card-body">
                                <h6 class="card-title">{{ $service->name }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit($service->description, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <strong class="text-success">Rp {{ number_format($service->price, 0, ',', '.') }}</strong>
                                    <small class="text-muted"><i class="bi bi-clock"></i> {{ $service->duration_minutes }} min</small>
                                </div>
                                <!-- Only show customer actions, no admin CRUD -->
                                <button type="button" class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#createBookingModal">
                                    <i class="bi bi-calendar-plus"></i> Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted text-center py-4">Tidak ada layanan tersedia.</p>
                    </div>
                @endforelse
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('customer.services') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-right"></i> Lihat Semua Layanan
                </a>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL: PEMESANAN SAYA -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalPemesanan" tabindex="-1" aria-labelledby="modalPemesananLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white;">
                    <h5 class="modal-title" id="modalPemesananLabel">
                        <i class="bi bi-calendar-check"></i> Pemesanan Saya
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($recentBookings->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Anda belum membuat pemesanan.</p>
                            <a href="{{ route('customer.services') }}" class="btn btn-primary mt-3" data-bs-dismiss="modal">
                                <i class="bi bi-plus-circle"></i> Buat Pemesanan
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="table-light">
                                        <th>Layanan</th>
                                        <th>Tanggal Jadwal</th>
                                        <th>Teknisi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td><strong>{{ $booking->service->name }}</strong></td>
                                            <td>{{ $booking->scheduled_date->format('d/m/Y H:i') }}</td>
                                            <td>{{ $booking->technician?->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge @switch($booking->status)
                                                    @case('pending') bg-warning text-dark @break
                                                    @case('confirmed') bg-info @break
                                                    @case('in_progress') bg-primary @break
                                                    @case('completed') bg-success @break
                                                    @case('cancelled') bg-danger @break
                                                @endswitch">
                                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="{{ route('customer.services') }}" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-plus-circle"></i> Buat Pemesanan Baru
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL: INVOICE SAYA -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalInvoice" tabindex="-1" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white;">
                    <h5 class="modal-title" id="modalInvoiceLabel">
                        <i class="bi bi-receipt"></i> Invoice Saya
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($unpaidInvoices->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                            <p class="text-success mt-3"><strong>Tidak ada invoice yang tertunggak.</strong></p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($unpaidInvoices as $invoice)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><strong>{{ $invoice->invoice_number }}</strong></h6>
                                            <p class="mb-2 text-muted small">
                                                <i class="bi bi-wrench"></i> {{ $invoice->booking->service->name }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event"></i>
                                                Dibuat: {{ $invoice->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <h6 class="text-danger"><strong>Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong></h6>
                                            <span class="badge bg-warning text-dark">{{ ucfirst($invoice->status) }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('customer.invoice.show', $invoice) }}" class="btn btn-sm btn-outline-primary" data-bs-dismiss="modal">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </a>
                                        <a href="{{ route('customer.payment.form', $invoice) }}" class="btn btn-sm btn-success" data-bs-dismiss="modal">
                                            <i class="bi bi-credit-card"></i> Bayar
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL: LAYANAN TERSEDIA -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalLayanan" tabindex="-1" aria-labelledby="modalLayananLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white;">
                    <h5 class="modal-title" id="modalLayananLabel">
                        <i class="bi bi-wrench"></i> Layanan Tersedia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @forelse(\App\Models\Service::all() as $service)
                            <div class="col-md-6 mb-3">
                                <div class="card border h-100" style="border-left: 4px solid #0066cc;">
                                    <div class="card-body">
                                        <h6 class="card-title" style="color: #0066cc; font-weight: bold;">{{ $service->name }}</h6>
                                        <p class="card-text text-muted small">{{ Str::limit($service->description, 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <small class="text-muted d-block">Harga</small>
                                                <strong class="text-success" style="font-size: 1.1rem;">Rp {{ number_format($service->price, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">Durasi</small>
                                                <strong class="text-primary">{{ $service->duration_minutes }} min</strong>
                                            </div>
                                        </div>
                                        <a href="{{ route('customer.services') }}" class="btn btn-sm btn-primary w-100" data-bs-dismiss="modal">
                                            <i class="bi bi-cart-plus"></i> Pesan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-3">Tidak ada layanan tersedia.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('customer.services') }}" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-right"></i> Lihat Semua Layanan
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
