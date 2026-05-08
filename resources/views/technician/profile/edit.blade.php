@extends('layouts.app')

@section('title', 'Edit Profil - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-pencil"></i>
        <h1>Edit Profil</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('technician.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $technician->user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label">Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $technician->phone) }}" required placeholder="08XXXXXXXXXX">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Specialization -->
                        <div class="mb-3">
                            <label class="form-label">Spesialisasi</label>
                            <input type="text" class="form-control @error('specialization') is-invalid @enderror" name="specialization" value="{{ old('specialization', $technician->specialization) }}" placeholder="Contoh: AC Split, AC Window">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Years Experience -->
                        <div class="mb-3">
                            <label class="form-label">Pengalaman (Tahun)</label>
                            <input type="number" class="form-control @error('years_experience') is-invalid @enderror" name="years_experience" value="{{ old('years_experience', $technician->years_experience) }}" min="0">
                            @error('years_experience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Profil</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4" placeholder="Ceritakan tentang keahlian dan pengalaman Anda..." maxlength="500">{{ old('description', $technician->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maksimal 500 karakter</small>
                        </div>

                        <!-- Photo -->
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" accept="image/*">
                                <small class="form-text text-muted d-block mt-2">Ukuran maksimal: 2MB. Format: JPEG, PNG, JPG, GIF</small>
                            </div>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($technician->photo)
                                <div class="mt-3">
                                    <label class="text-muted small">Foto Saat Ini</label><br>
                                    <img src="{{ asset('storage/' . $technician->photo) }}" alt="Profil" style="max-width: 150px; border-radius: 8px;">
                                </div>
                            @endif
                        </div>

                        <!-- Submit -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('technician.profile.show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
