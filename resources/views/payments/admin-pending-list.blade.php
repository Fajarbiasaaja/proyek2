@extends('layouts.app')

@section('title', 'Monitoring Pembayaran')

@section('content')
    <div class="page-title">
        <i class="bi bi-credit-card"></i>
        <h1>Monitoring Pembayaran</h1>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <h5>Menunggu Verifikasi</h5>
                <div class="number">{{ $payments->total() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card success">
                <h5>Total Nilai Pending</h5>
                <div class="number" style="font-size: 1.5rem;">Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h5>Halaman</h5>
                <div class="number">{{ $payments->currentPage() }} / {{ $payments->lastPage() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card success">
                <h5>Per Halaman</h5>
                <div class="number">{{ count($payments) }}</div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Daftar Pembayaran Menunggu Verifikasi</h5>
        </div>
        <div class="table-responsive">
            @if($payments->count() > 0)
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 10%;">No Pembayaran</th>
                            <th style="width: 15%;">Invoice</th>
                            <th style="width: 15%;">Pelanggan</th>
                            <th style="width: 12%;">Metode</th>
                            <th style="width: 12%;">Jumlah</th>
                            <th style="width: 15%;">Tanggal Submit</th>
                            <th style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>
                                    <strong>#PM{{ $payment->id }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $payment->invoice->invoice_number }}</strong><br>
                                    <small class="text-muted">{{ $payment->invoice->booking->service->name }}</small>
                                </td>
                                <td>
                                    <strong>{{ $payment->invoice->booking->customer->name }}</strong><br>
                                    <small class="text-muted">{{ $payment->invoice->booking->customer->email }}</small>
                                </td>
                                <td>
                                    @php
                                        $methodLabel = [
                                            'cash' => 'Tunai',
                                            'bank_transfer' => 'Transfer Bank',
                                            'e_wallet' => 'E-Wallet',
                                            'credit_card' => 'Kartu Kredit'
                                        ];
                                        $methodColor = [
                                            'cash' => 'success',
                                            'bank_transfer' => 'info',
                                            'e_wallet' => 'warning',
                                            'credit_card' => 'primary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $methodColor[$payment->payment_method] ?? 'secondary' }}">
                                        {{ $methodLabel[$payment->payment_method] ?? $payment->payment_method }}
                                    </span>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    {{ $payment->submitted_date->format('d/m/Y H:i') }}<br>
                                    <small class="text-muted">{{ $payment->submitted_date->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding: 40px; text-align: center; background: #f8f9fa;">
                    <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745; margin-bottom: 10px;"></i>
                    <p style="font-size: 1.1rem; color: #666; margin-top: 10px;">
                        <strong>Tidak ada pembayaran yang menunggu verifikasi</strong>
                    </p>
                    <p class="text-muted">Semua pembayaran sudah diverifikasi atau disetujui</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($payments->hasPages())
        <div class="mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    {{-- Previous Page Link --}}
                    @if ($payments->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">← Sebelumnya</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->previousPageUrl() }}">← Sebelumnya</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                        @if ($page == $payments->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($payments->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->nextPageUrl() }}">Selanjutnya →</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Selanjutnya →</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif

    <!-- Info Section -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-exclamation-circle"></i> Instruksi Verifikasi</h6>
                    <ul class="small mb-0">
                        <li>Klik tombol <strong>"Lihat Detail"</strong> untuk melihat bukti pembayaran</li>
                        <li>Verifikasi bukti dengan nomor referensi yang diberikan customer</li>
                        <li>Periksa apakah amount yang ditransfer sesuai dengan invoice</li>
                        <li>Klik <strong>"Setujui"</strong> jika pembayaran valid</li>
                        <li>Klik <strong>"Tolak"</strong> jika ada masalah (akan memberi notif ke customer)</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-check-circle"></i> Metode Pembayaran</h6>
                    <ul class="small mb-0">
                        <li><strong>Tunai:</strong> Pembayaran saat service, cek foto uang/struk</li>
                        <li><strong>Transfer Bank:</strong> Verifikasi nomor referensi dari bank</li>
                        <li><strong>E-Wallet:</strong> Verifikasi nomor referensi dari e-wallet</li>
                        <li><strong>Kartu Kredit:</strong> Verifikasi screenshot transaksi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
