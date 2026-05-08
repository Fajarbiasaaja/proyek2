@extends('layouts.app')

@section('title', 'Profil Saya - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-person"></i>
        <h1>Profil Saya</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Profile Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Nama</label>
                                <p class="mb-0"><strong>{{ $technician->user->name }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Email</label>
                                <p class="mb-0"><strong>{{ $technician->user->email }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Telepon</label>
                                <p class="mb-0"><strong>{{ $technician->phone ?? '-' }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Spesialisasi</label>
                                <p class="mb-0"><strong>{{ $technician->specialization ?? 'Belum diatur' }}</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Pengalaman</label>
                                <p class="mb-0"><strong>{{ $technician->years_experience ?? 0 }} Tahun</strong></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <p class="mb-0">
                                    <span class="badge @if($technician->status === 'available') bg-success @elseif($technician->status === 'busy') bg-warning @else bg-secondary @endif">
                                        {{ ucfirst($technician->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($technician->description)
                        <div class="mt-3 pt-3 border-top">
                            <label class="text-muted small">Deskripsi</label>
                            <p class="mb-0">{{ $technician->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Edit Button -->
            <div class="mb-3">
                <a href="{{ route('technician.profile.edit') }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Profil
                </a>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Pekerjaan Selesai</span>
                            <strong class="h5">{{ $stats['total_completed'] }}</strong>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Rating Rata-rata</span>
                            <strong class="h5">
                                <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                {{ number_format($stats['average_rating'], 1) }}/5
                            </strong>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Review</span>
                            <strong class="h5">{{ $stats['total_reviews'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            @if($technician->photo)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-image"></i> Foto Profil</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $technician->photo) }}" alt="Profil" class="img-fluid rounded" style="max-width: 200px;">
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
