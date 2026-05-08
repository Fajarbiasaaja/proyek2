@extends('layouts.app')

@section('title', 'Rating & Review Saya - Technician')

@section('content')
    <div class="page-title">
        <i class="bi bi-star"></i>
        <h1>Rating & Review</h1>
    </div>

    <!-- Rating Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #ffc107;">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <span style="font-size: 3rem;">
                            @php
                                $rating = $stats['average_rating'] ?? 0;
                                $stars = floor($rating);
                                $hasHalf = ($rating - $stars) >= 0.5;
                            @endphp
                            @for($i = 0; $i < $stars; $i++)
                                <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                            @endfor
                            @if($hasHalf)
                                <i class="bi bi-star-half" style="color: #ffc107;"></i>
                            @endif
                            @for($i = 0; $i < (5 - $stars - ($hasHalf ? 1 : 0)); $i++)
                                <i class="bi bi-star" style="color: #ddd;"></i>
                            @endfor
                        </span>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['average_rating'], 1) }}/5.0</h3>
                    <p class="text-muted">Rating Rata-rata</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #0066cc;">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $stats['total_ratings'] ?? 0 }}</h3>
                    <p class="text-muted">Total Review</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #28a745;">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="bi bi-hand-thumbs-up" style="font-size: 2rem; color: #28a745;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Kepuasan Pelanggan</h6>
                    <h4 class="mb-0">{{ $stats['average_rating'] >= 4 ? 'Sangat Baik' : ($stats['average_rating'] >= 3 ? 'Baik' : 'Cukup') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Breakdown -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Breakdown Rating</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($stats['rating_breakdown'] ?? [] as $starLabel => $count)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div style="width: 80px;">
                                <small class="text-muted">{{ $starLabel }}</small>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 20px;">
                                    @php
                                        $total = array_sum($stats['rating_breakdown'] ?? []);
                                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div style="width: 50px; text-align: right;">
                                <strong>{{ $count }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Ulasan Pelanggan</h5>
        </div>

        @if($ratings->count() > 0)
            <div class="card-body">
                @foreach($ratings as $rating)
                    <div class="border-bottom pb-3 mb-3 @if($loop->last) border-0 @endif">
                        <!-- Review Header -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">{{ $rating->booking->customer->name }}</h6>
                                <small class="text-muted">{{ $rating->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <div>
                                @for($i = 0; $i < $rating->rating; $i++)
                                    <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                @endfor
                                @for($i = 0; $i < (5 - $rating->rating); $i++)
                                    <i class="bi bi-star" style="color: #ddd;"></i>
                                @endfor
                            </div>
                        </div>

                        <!-- Review Details -->
                        <div class="mb-2">
                            <strong>{{ $rating->booking->service->name }}</strong><br>
                            <small class="text-muted">Order #{{ $rating->booking->id }}</small>
                        </div>

                        <!-- Review Comment -->
                        @if($rating->comment)
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $rating->comment }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <span class="text-muted small">Menampilkan {{ $ratings->firstItem() }} - {{ $ratings->lastItem() }} dari {{ $ratings->total() }} review</span>
                {{ $ratings->links() }}
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="bi bi-star" style="font-size: 3rem; color: #999;"></i>
                <h5 class="mt-3">Belum ada review</h5>
                <p class="text-muted">Review dari pelanggan akan muncul setelah mereka menilai pekerjaan Anda.</p>
            </div>
        @endif
    </div>
@endsection
