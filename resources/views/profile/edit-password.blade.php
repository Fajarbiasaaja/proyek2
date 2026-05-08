@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
    <div class="page-title">
        <i class="bi bi-key"></i>
        <h1>Ubah Password</h1>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-key"></i> Ubah Password
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.updatePassword') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini *</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Minimal 8 karakter" required>
                            <small class="form-text text-muted">Gunakan kombinasi huruf, angka, dan simbol untuk password yang kuat.</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru *</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Ubah Password
                            </button>
                            <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('customer.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-shield-check"></i> Saran Password Kuat
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Jangan lupa password baru Anda!</strong>
                    </div>

                    <h6>Password yang kuat harus memiliki:</h6>
                    <ul>
                        <li>Minimal <strong>8 karakter</strong></li>
                        <li>Kombinasi <strong>huruf besar</strong> (A-Z)</li>
                        <li>Kombinasi <strong>huruf kecil</strong> (a-z)</li>
                        <li>Kombinasi <strong>angka</strong> (0-9)</li>
                        <li>Kombinasi <strong>simbol</strong> (!@#$%^&*)</li>
                    </ul>

                    <h6 class="mt-4">Contoh Password Kuat:</h6>
                    <div class="bg-light p-2 rounded">
                        <code>ServisAC2025@!</code><br>
                        <code>NewPass#2025Aman</code><br>
                        <code>Teknisi@AC123</code>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Tip:</strong> Simpan password di tempat yang aman dan jangan bagikan kepada siapa pun.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
