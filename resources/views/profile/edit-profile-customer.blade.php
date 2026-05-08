@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <div class="page-title">
        <i class="bi bi-person"></i>
        <h1>Edit Profil</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Edit Data Profil
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.updateProfile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" 
                                   value="{{ $user->email }}" disabled>
                            <small class="form-text text-muted">
                                <a href="{{ route('profile.editEmail') }}">Klik di sini untuk mengubah email</a>
                            </small>
                        </div>

                        @if($customer)
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat *</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address', $customer->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">Kota</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $customer->city ?? '') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" name="postal_code" value="{{ old('postal_code', $customer->postal_code ?? '') }}">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-key"></i> Keamanan Akun
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('profile.editEmail') }}" class="btn btn-outline-primary">
                        <i class="bi bi-envelope"></i> Ubah Email
                    </a>
                    <a href="{{ route('profile.editPassword') }}" class="btn btn-outline-warning">
                        <i class="bi bi-key"></i> Ubah Password
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Informasi Akun
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Email:</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Terdaftar:</th>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
