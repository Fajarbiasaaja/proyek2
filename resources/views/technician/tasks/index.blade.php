@extends('layouts.app')

@section('title', 'Tugas Saya - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-list-check"></i>
        <h1>Tugas Saya</h1>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #0066cc;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Menunggu Dikerjakan</h6>
                            <h3 class="mb-0">{{ $stats['pending_tasks'] ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #0066cc; opacity: 0.2;">
                            <i class="bi bi-hourglass-top"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #ff6b35;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Sedang Dikerjakan</h6>
                            <h3 class="mb-0">{{ $stats['in_progress_tasks'] ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #ff6b35; opacity: 0.2;">
                            <i class="bi bi-hammer"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Selesai Hari Ini</h6>
                            <h3 class="mb-0">{{ $stats['completed_today'] ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #28a745; opacity: 0.2;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Tugas</h6>
                            <h3 class="mb-0">{{ $stats['pending_tasks'] + $stats['in_progress_tasks'] }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: #ffc107; opacity: 0.2;">
                            <i class="bi bi-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Daftar Tugas</h5>
        </div>

        @if($tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Pelanggan</th>
                            <th style="width: 18%;">Layanan</th>
                            <th style="width: 18%;">Tanggal Jadwal</th>
                            <th style="width: 12%;">Harga</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 25%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>
                                    <strong>{{ $task->customer->name }}</strong><br>
                                    <small class="text-muted">{{ $task->customer->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $task->service->name }}
                                </td>
                                <td>
                                    {{ $task->scheduled_date->format('d/m/Y H:i') }}<br>
                                    <small class="text-muted">{{ $task->scheduled_date->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($task->total_price, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="badge @if($task->status === 'confirmed') bg-info @elseif($task->status === 'in_progress') bg-warning text-dark @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('technician.tasks.show', $task) }}" class="btn btn-outline-primary" title="Lihat Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>

                                        @if($task->status === 'confirmed')
                                            <form action="{{ route('technician.tasks.start', $task) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" title="Mulai Pekerjaan">
                                                    <i class="bi bi-play-fill"></i> Mulai
                                                </button>
                                            </form>
                                        @endif

                                        @if($task->status === 'in_progress')
                                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#completeModal{{ $task->id }}" title="Selesaikan Tugas">
                                                <i class="bi bi-check-lg"></i> Selesai
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Complete Task Modal -->
                            @if($task->status === 'in_progress')
                                <div class="modal fade" id="completeModal{{ $task->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Selesaikan Tugas #{{ $task->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('technician.tasks.complete', $task) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan Penyelesaian <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" name="completion_notes" rows="4" required placeholder="Jelaskan pekerjaan yang telah dilakukan..."></textarea>
                                                        @error('completion_notes')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-check-lg"></i> Konfirmasi Selesai
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <span class="text-muted small">Menampilkan {{ $tasks->firstItem() }} - {{ $tasks->lastItem() }} dari {{ $tasks->total() }} tugas</span>
                {{ $tasks->links() }}
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #999;"></i>
                <h5 class="mt-3">Tidak ada tugas</h5>
                <p class="text-muted">Anda tidak memiliki tugas yang sedang menunggu atau sedang dikerjakan.</p>
            </div>
        @endif
    </div>
@endsection
