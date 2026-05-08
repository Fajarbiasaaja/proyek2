@extends('layouts.app')

@section('title', 'Ubah Email')

@section('content')
    <div class="page-title">
        <i class="bi bi-envelope"></i>
        <h1>Ubah Email</h1>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-envelope"></i> Ubah Email
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i> Email saat ini: <strong>{{ auth()->user()->email }}</strong>
                    </div>

                    <form action="{{ route('profile.updateEmail') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Baru *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Konfirmasi Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Masukkan password Anda untuk konfirmasi" required>
                            <small class="form-text text-muted">Kami butuh password untuk konfirmasi perubahan.</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Ubah Email
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
                    <i class="bi bi-shield-lock"></i> Tips Keamanan
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Email Unik</strong><br>
                            <small class="text-muted">Email yang Anda masukkan tidak boleh sudah digunakan.</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Konfirmasi Password</strong><br>
                            <small class="text-muted">Masukkan password Anda untuk keamanan tambahan.</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Email Valid</strong><br>
                            <small class="text-muted">Pastikan email format benar (contoh: user@example.com).</small>
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success"></i>
                            <strong>Update Immidiat</strong><br>
                            <small class="text-muted">Perubahan email langsung berlaku setelah dikonfirmasi.</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
