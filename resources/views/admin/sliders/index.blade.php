@extends('layouts.app')

@section('title', 'Manage Sliders')

@section('content')
    <div class="page-title">
        <i class="bi bi-images"></i>
        <h1>Manage Sliders</h1>
    </div>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list"></i> Daftar Slider
                </h5>
                <a href="{{ route('sliders.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Tambah Slider
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($sliders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Preview</th>
                                <th>Judul</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sliders as $index => $slider)
                                <tr>
                                    <td>{{ ($sliders->currentPage() - 1) * $sliders->perPage() + $index + 1 }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $slider->image) }}" 
                                             alt="{{ $slider->title }}" 
                                             style="height: 60px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td>
                                        <strong>{{ $slider->title ?? 'Tanpa Judul' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($slider->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $slider->sort_order }}</span>
                                    </td>
                                    <td>
                                        @if($slider->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('sliders.edit', $slider) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('sliders.toggleActive', $slider) }}" 
                                                  method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-outline-warning" 
                                                        title="{{ $slider->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="bi bi-eye{{ $slider->is_active ? '' : '-slash' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('sliders.destroy', $slider) }}" 
                                                  method="POST" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Yakin hapus slider ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox"></i> Belum ada slider
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sliders->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $sliders->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Belum ada slider. Buat slider pertama Anda sekarang!</p>
                    <a href="{{ route('sliders.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus"></i> Tambah Slider
                    </a>
                </div>
            @endif
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

        .badge {
            padding: 6px 10px;
            font-weight: 500;
        }
    </style>
@endsection
