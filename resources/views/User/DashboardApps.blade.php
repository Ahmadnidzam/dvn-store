@extends('Layouts.layout')
@section('title', 'Dashboard Apps - DVNStore')

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/dashboard.css') }}">
@endpush

@section('content')

<div class="mb-5">
    @include('Components.carousel')
</div>

<nav class="mb-5">
   @include('Components.nav')
</nav>

<section class="mb-5">
    <div class="section-header">
        <h3 class="fw-bold section-title mb-0">Popular Apps</h3>
        <a href="{{ url('/top/app') }}" class="view-all-link">
            View All <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_forward</span>
        </a>
    </div>

    @if($apps->isEmpty())
        <div class="text-center text-muted py-4">Belum ada aplikasi tersedia.</div>
    @else
        <div class="scroll-container d-flex gap-4 overflow-auto pb-2">
            @foreach ($apps->sortByDesc('rating')->take(8) as $app)
            <div class="product-card" style="min-width: 260px;">
                <a href="{{ route('lable', $app->id) }}">
                    <div class="position-relative">
                        <div class="product-image">
                            <img src="{{ asset('storage/' . $app->icon) }}" alt="{{ $app->nama_platform }}" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">{{ $app->genre }}</div>
                        <h5 class="product-title">{{ Str::limit($app->nama_platform, 20) }}</h5>
                        <div class="product-rating">
                            <span class="stars">★</span>
                            <span class="rating-text">{{ $app->rating }}</span>
                        </div>
                        <div class="product-price">
                            @if($app->harga == 0)
                                <span class="price-tag text-success">Free</span>
                            @else
                                <span class="price-tag">Rp{{ number_format($app->harga, 0, ',', '.') }}</span>
                            @endif
                            <button class="btn btn-primary btn-add">Get</button>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @endif
</section>

{{-- SECTION 2: ALL APPS --}}
<section class="mb-5">
    <div class="section-header">
        <h3 class="fw-bold section-title mb-0">Semua Aplikasi untuk Produktivitasmu</h3>
        <a href="{{ url('/all/app') }}" class="view-all-link">
            View All <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_forward</span>
        </a>
    </div>

    @if($apps->isEmpty())
        <div class="text-center text-muted py-4">Belum ada aplikasi tersedia.</div>
    @else
        <div class="scroll-container d-flex gap-4 overflow-auto pb-2">
            @foreach ($apps as $app)
            <div class="product-card" style="min-width: 260px;">
                <a href="{{ route('lable', $app->id) }}">
                    <div class="position-relative">
                        <div class="product-image">
                            <img src="{{ asset('storage/' . $app->icon) }}" alt="{{ $app->nama_platform }}" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">{{ $app->genre }}</div>
                        <h5 class="product-title">{{ Str::limit($app->nama_platform, 20) }}</h5>
                        <div class="product-rating">
                            <span class="stars">★</span>
                            <span class="rating-text">{{ $app->rating }}</span>
                        </div>
                        <div class="product-price">
                            @if($app->harga == 0)
                                <span class="price-tag text-success">Free</span>
                            @else
                                <span class="price-tag">Rp{{ number_format($app->harga, 0, ',', '.') }}</span>
                            @endif
                            <button class="btn btn-primary btn-add">Get</button>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @endif
</section>

@endsection