@extends('layouts.app')

@section('title', 'Tambah Slider')

@section('content')
    <div class="page-title">
        <i class="bi bi-plus-circle"></i>
        <h1>Tambah Slider Baru</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-form-check"></i> Form Slider</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*" required onchange="previewImage()">
                            </div>
                            <small class="text-muted d-block mt-2">
                                Format: JPG, PNG, GIF, WebP | Ukuran max: 5MB | Rekomendasi: 1920x500px
                            </small>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="mt-3">
                                <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 4px; display: none;">
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" placeholder="Judul slider">
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Deskripsi slider...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Button Text -->
                        <div class="mb-3">
                            <label for="button_text" class="form-label">Teks Tombol</label>
                            <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                                   id="button_text" name="button_text" value="{{ old('button_text') }}" 
                                   placeholder="Contoh: Lihat Layanan, Hubungi Kami">
                            @error('button_text')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Button Link -->
                        <div class="mb-3">
                            <label for="button_link" class="form-label">Link Tombol</label>
                            <input type="url" class="form-control @error('button_link') is-invalid @enderror" 
                                   id="button_link" name="button_link" value="{{ old('button_link') }}" 
                                   placeholder="https://example.com">
                            @error('button_link')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Urutan</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                   min="0" placeholder="0">
                            <small class="text-muted">Slider dengan urutan kecil akan ditampilkan lebih dulu</small>
                            @error('sort_order')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktifkan Slider</strong> - Slider akan ditampilkan di dashboard
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan Slider
                            </button>
                            <a href="{{ route('sliders.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Tips</h5>
                </div>
                <div class="card-body small">
                    <h6>Ukuran Gambar Optimal</h6>
                    <p class="mb-2">Gunakan ukuran: <strong>1920 x 500 px</strong> untuk hasil terbaik</p>

                    <h6>Rekomendasi</h6>
                    <ul class="mb-0">
                        <li>Gunakan gambar berkualitas tinggi</li>
                        <li>Sesuaikan dengan tema layanan AC</li>
                        <li>Tambahkan teks yang menarik</li>
                        <li>Link tombol bisa ke halaman lain atau external link</li>
                        <li>Maksimal 5-7 slider aktif untuk performa optimal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .form-label {
            font-weight: 500;
        }

        .text-danger {
            color: #dc3545;
        }
    </style>

    <script>
        function previewImage() {
            const file = document.getElementById('image').files[0];
            const preview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
