@extends('Layouts.layout')
@section('title', $platform->nama_platform . ' - Admin')
@section('content')
<a href="{{ route('admin.platforms.index') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
<div class="row g-3">
    <div class="col-md-4">
        @if($platform->icon)<img src="{{ asset('storage/'.$platform->icon) }}" class="img-fluid rounded mb-3">@endif
        <p><strong>Developer:</strong> {{ $platform->developer->name ?? '-' }}</p>
        <p><strong>Kategori:</strong> {{ $platform->category }}</p>
        <p><strong>Genre:</strong> {{ $platform->genre }}</p>
        <p><strong>Harga:</strong> Rp {{ number_format($platform->harga,0,',','.') }}</p>
        <p><strong>Rating:</strong> <span class="rating-inline"><span class="material-symbols-outlined">star</span> {{ number_format($platform->rating,2) }}</span></p>
        <p><strong>Scan Status:</strong> <span class="badge bg-info">{{ $platform->scan_status }}</span></p>
        @if($platform->scan_result)
            <pre class="bg-light p-2 small">{{ json_encode($platform->scan_result, JSON_PRETTY_PRINT) }}</pre>
        @endif
    </div>
    <div class="col-md-8">
        <h4 class="fw-bold">{{ $platform->nama_platform }}</h4>
        @if($platform->is_taken_down)
            <div class="alert alert-dark">
                <strong>Taken Down</strong><br>Alasan: {{ $platform->taken_down_reason }}<br>
                Tanggal: {{ $platform->taken_down_at->format('d M Y H:i') }}
            </div>
        @endif
        <p>{{ $platform->deskripsi }}</p>
        <h6 class="fw-bold mt-4">Review ({{ $platform->reviews->count() }})</h6>
        @foreach($platform->reviews as $r)
            <div class="border rounded p-3 mb-2">
                <strong>{{ $r->user->name }}</strong> - <span class="rating-inline"><span class="material-symbols-outlined">star</span> {{ $r->rating }}</span> <small class="text-muted">({{ $r->created_at->diffForHumans() }})</small>
                <p class="mb-0 mt-1">{{ $r->komentar }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
