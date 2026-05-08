@extends('layouts.app')

@section('title', 'Daftar Teknisi')

@section('content')
    <div class="page-title">
        <i class="bi bi-tools"></i>
        <h1>Daftar Teknisi</h1>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-tools"></i> Teknisi</span>
            <a href="{{ route('technicians.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Teknisi
            </a>
        </div>
        <div class="card-body">
            @if($technicians->isEmpty())
                <p class="text-muted mb-0">Tidak ada teknisi.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Spesialisasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($technicians as $technician)
                                <tr>
                                    <td>{{ $technician->name }}</td>
                                    <td>{{ $technician->email }}</td>
                                    <td>{{ $technician->phone }}</td>
                                    <td>{{ $technician->specialization }}</td>
                                    <td>
                                        <span class="badge @if($technician->status === 'available') bg-success @elseif($technician->status === 'busy') bg-warning @else bg-danger @endif">
                                            {{ ucfirst($technician->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('technicians.show', $technician) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('technicians.edit', $technician) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('technicians.destroy', $technician) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $technicians->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
