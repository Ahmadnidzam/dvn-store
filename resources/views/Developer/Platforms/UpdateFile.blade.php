@extends('Layouts.layout')
@section('title','Update File Installer - Developer')
@section('content')
<a href="{{ route('developer.platforms.index') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>

<h3 class="page-title">
    <span class="material-symbols-outlined">cloud_upload</span>
    Update File Installer
</h3>
<p class="text-muted mb-4">{{ $platform->nama_platform }} <span class="badge bg-secondary ms-1">{{ $platform->category }}</span></p>

@php
    $pending = $platform->hasPendingUpdate();
    $pendingStatus = $platform->pending_scan_status;
@endphp

<div class="alert alert-info d-flex align-items-start gap-2">
    <span class="material-symbols-outlined">info</span>
    <div class="small">
        <strong>Cara kerja:</strong> File baru akan dipindai keamanannya oleh VirusTotal terlebih dahulu.
        Selama proses pemindaian, file lama tetap aktif untuk pengguna.
        Jika pemindaian <strong>bersih</strong>, file lama otomatis dihapus dan digantikan.
        Jika <strong>terdeteksi malware</strong>, file pengganti ditolak dan file lama tetap aktif.
        Update file <strong>tidak dikenakan biaya tambahan</strong>.
    </div>
</div>

@if($pending)
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Status Pembaruan</h5>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            @switch($pendingStatus)
                @case('pending')
                    <span class="badge bg-warning text-dark"><span class="material-symbols-outlined" style="font-size:.95rem">schedule</span> Menunggu antrian scan</span>
                    @break
                @case('scanning')
                    <span class="badge bg-info"><span class="material-symbols-outlined" style="font-size:.95rem">security</span> Sedang dipindai VirusTotal</span>
                    @break
                @case('error')
                    <span class="badge bg-danger"><span class="material-symbols-outlined" style="font-size:.95rem">error</span> Gagal scan (perlu dibatalkan & coba lagi)</span>
                    @break
                @case('infected')
                    <span class="badge bg-danger"><span class="material-symbols-outlined" style="font-size:.95rem">gpp_bad</span> Ditolak karena terdeteksi malware</span>
                    @break
                @default
                    <span class="badge bg-secondary">{{ $pendingStatus }}</span>
            @endswitch

            <small class="text-muted">
                Diunggah {{ optional($platform->pending_uploaded_at)->diffForHumans() }} —
                ukuran {{ number_format($platform->pending_file_size / 1024 / 1024, 2) }} MB
            </small>
        </div>

        @if(in_array($pendingStatus, ['pending','scanning','error'], true))
        <form action="{{ route('developer.platforms.cancel-pending', $platform->id) }}" method="POST" class="mt-3"
              onsubmit="return confirm('Batalkan pembaruan ini? File pengganti akan dihapus.');">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm">
                <span class="material-symbols-outlined">close</span> Batalkan Pembaruan
            </button>
        </form>
        @endif
    </div>
</div>
@endif

@if(!$pending || $pendingStatus === 'infected')
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Unggah File Pengganti</h5>
        <form method="POST" action="{{ route('developer.platforms.update-file.store', $platform->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">File Installer Baru</label>
                <input type="file" name="file" class="form-control" required
                       accept=".zip,.apk,.exe,.7z,.rar">
                <small class="text-muted">Format: ZIP, APK, EXE, 7Z, RAR. Maksimum 200 MB.</small>
            </div>

            @if($platform->file_path)
            <div class="alert alert-secondary small mb-3">
                <strong>File aktif saat ini:</strong>
                {{ basename($platform->file_path) }}
                ({{ number_format($platform->file_size / 1024 / 1024, 2) }} MB)
                @if($platform->file_updated_at)
                    — diperbarui {{ $platform->file_updated_at->diffForHumans() }}
                @endif
            </div>
            @endif

            <button class="btn btn-success">
                <span class="material-symbols-outlined">upload</span> Unggah & Pindai
            </button>
        </form>
    </div>
</div>
@endif
@endsection
