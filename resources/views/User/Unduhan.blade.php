@extends('Layouts.layout')

@section('title', 'Unduhan - DVNStore')

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/unduh.css') }}">
@endpush

@section('content')
  <a href="javascript:history.back()" class="btn-back">
    <span class="material-symbols-outlined">arrow_back</span>
    <span class="btn-text">Kembali</span>
  </a>

<div class="download-page">
    <div class="download-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="mb-2"><i class="fas fa-download me-2"></i>Unduhan Saya</h2>
                <p class="mb-0 opacity-75">Kelola semua aplikasi dan game yang Anda miliki</p>
            </div>
            <div class="text-end">
                <h3 class="mb-0">{{ $downloads->count() }}</h3>
                <small>Total Item</small>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h5 class="mb-3 text-muted">
            <i class="fas fa-check-circle me-2"></i>Library Anda
        </h5>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @forelse ($downloads as $download)
        @php
            $platform = $download->platform;
            $hasUpdate = $platform
                && $platform->file_updated_at
                && $download->updated_at->lt($platform->file_updated_at);
            $isAvailable = $platform
                && $platform->file_path
                && $platform->scan_status === 'clean'
                && !$platform->is_taken_down;
        @endphp
        <div class="download-card mb-3">
            <div class="d-flex gap-3 align-items-center">

                <div class="download-icon">
                    <a href="{{ route('lable', $platform->id) }}">
                        <img src="{{ asset('storage/' . $platform->icon) }}"
                             alt="{{ $platform->nama_platform }}"
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                    </a>
                </div>

                <div class="download-info flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <div class="download-title fw-bold">
                            <a href="{{ route('lable', $platform->id) }}" class="text-decoration-none text-dark">
                                {{ $platform->nama_platform }}
                            </a>
                        </div>
                        <span class="badge bg-success badge-status" style="font-size: 0.7rem;">Dimiliki</span>
                        @if($hasUpdate)
                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                <span class="material-symbols-outlined" style="font-size:.85rem">new_releases</span>
                                Update tersedia
                            </span>
                        @endif
                    </div>
                    <div class="download-subtitle text-muted small">
                        {{ optional($platform->developer)->name ?? 'Unknown Developer' }}
                    </div>

                    <div class="download-status mt-1">
                        @if($isAvailable)
                            <span class="status-text text-success small">
                                <i class="fas fa-check-circle me-1"></i>Siap digunakan
                            </span>
                        @else
                            <span class="status-text text-danger small">
                                <i class="fas fa-times-circle me-1"></i>Tidak tersedia
                            </span>
                        @endif
                        <span class="text-muted small ms-2">
                            • Ditambahkan {{ $download->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>

                <div class="download-actions d-flex align-items-center gap-2">
                    @if($isAvailable)
                        <a href="{{ route('unduhan.file', $download->id) }}"
                           class="btn btn-sm {{ $hasUpdate ? 'btn-warning' : 'btn-primary' }}">
                            <span class="material-symbols-outlined" style="font-size:.95rem">{{ $hasUpdate ? 'system_update' : 'download' }}</span>
                            {{ $hasUpdate ? 'Update' : 'Download' }}
                        </a>
                    @endif
                    <form action="{{ route('unduhan.destroy', $download->id) }}" method="POST"
                          onsubmit="return confirm('Hapus {{ $platform->nama_platform }} dari library?');" class="m-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon btn-cancel border-0 bg-transparent text-danger p-2" title="Hapus dari Library">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="text-center py-5 text-muted border rounded bg-light">
                <span class="material-symbols-outlined fs-1">download</span>
                <p class="mt-2">Anda belum memiliki unduhan apapun.</p>
                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm mt-2">Jelajahi Store</a>
            </div>
        @endforelse
    </div>
</div>
@endsection