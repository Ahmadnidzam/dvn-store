@extends('Layouts.layout')
@section('title', 'Hasil Pencarian - DVNStore')

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/dashboard.css') }}">
@endpush

@section('content')

{{-- Tombol Kembali --}}
<a href="javascript:history.back()" class="btn-back">
    <span class="material-symbols-outlined">arrow_back</span>
    <span class="btn-text">Kembali</span>
</a>

<section class="mb-5">
    <div class="section-header">
        <h3 class="fw-bold section-title mb-0">
            @if($query)
                Hasil Pencarian untuk: "{{ $query }}"
            @else
                Hasil Pencarian
            @endif
        </h3>
    </div>

    <div class="row g-4">
        {{-- Cek Jika Hasil Kosong --}}
        @if($search->isEmpty())
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3">
                    <span class="material-symbols-outlined" style="font-size: 3rem;">search_off</span>
                </div>
                <h5>Tidak ditemukan hasil untuk "{{ $query }}"</h5>
                <p class="text-secondary">Coba gunakan kata kunci lain atau periksa ejaan Anda.</p>
            </div>
        @else
            @foreach($search as $result)
            <div class="col-12">
                <a href="{{ route('lable', $result->id) }}" class="text-decoration-none text-dark">
                    <div class="product-card d-flex flex-md-row flex-column align-items-center p-2" style="min-height: 180px;">
                        
                        <div class="product-image" style="flex: 0 0 200px; height: 180px; border-radius: 10px; overflow: hidden;">
                            <img src="{{ asset('storage/' . $result->icon) }}" alt="{{ $result->nama_platform }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <div class="product-info flex-grow-1 ps-md-4 pt-3 pt-md-0 w-100">
                            <div class="product-category mb-1 text-uppercase text-primary small fw-bold">
                                {{ $result->category }} • {{ $result->genre }}
                            </div>
                            
                            <h5 class="product-title mb-2">{{ $result->nama_platform }}</h5>
                            <p class="text-muted small mb-2 d-none d-md-block">{{ Str::limit($result->deskripsi, 120) }}</p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <div class="product-rating">
                                        <span class="stars">★</span>
                                        <span class="rating-text">{{ $result->rating }}</span>
                                    </div>
                                    
                                    @if($result->harga == 0)
                                        <span class="price-tag text-success fw-bold">Free</span>
                                    @else
                                        <span class="price-tag fw-bold">Rp{{ number_format($result->harga, 0, ',', '.') }}</span>
                                    @endif
                                </div>

                                <button class="btn btn-primary btn-add">Get</button>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        @endif
    </div>
</section>
@endsection