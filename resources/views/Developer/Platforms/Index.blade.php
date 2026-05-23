@extends('Layouts.layout')
@section('title','Produk Saya - Developer')
@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
    <div>
        <h3 class="page-title mb-1"><span class="material-symbols-outlined">inventory_2</span> Produk Saya</h3>
        <p class="page-kicker mb-0">Kelola listing, status pembayaran upload fee, hasil scan, dan publikasi produk.</p>
    </div>
    <a href="{{ route('developer.platforms.create') }}" class="btn btn-success">
        <span class="material-symbols-outlined align-middle">add</span> Upload Baru
    </a>
</div>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table align-middle">
<thead><tr><th>Icon</th><th>Nama</th><th>Kategori</th><th class="text-end">Harga</th><th>Scan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
<tbody>
@forelse($platforms as $p)
@php
    $uploadFee = $p->uploadFeeTransaksi;
    $isUploadFeePaid = $uploadFee && $uploadFee->status === 'paid';
    $hasPendingPayment = !$isUploadFeePaid && (!$uploadFee || in_array($uploadFee->status, ['pending', 'failed', 'expired', 'cancelled'], true));
@endphp
<tr>
    <td>@if($p->icon)<img src="{{ asset('storage/'.$p->icon) }}" class="object-thumb" style="width:50px;height:50px" alt="{{ $p->nama_platform }}">@endif</td>
    <td><strong>{{ $p->nama_platform }}</strong><br><small class="text-muted">{{ $p->genre }}</small></td>
    <td><span class="badge bg-secondary">{{ $p->category }}</span></td>
    <td class="text-end">Rp {{ number_format($p->harga,0,',','.') }}</td>
    <td>
        @if(!$isUploadFeePaid)
            <span class="badge bg-secondary">Belum scan</span>
        @else
            @switch($p->scan_status)
                @case('clean')<span class="badge bg-success">Clean</span>@break
                @case('infected')<span class="badge bg-danger">Infected</span>@break
                @case('scanning')<span class="badge bg-info">Scanning</span>@break
                @case('pending')<span class="badge bg-warning text-dark">Menunggu scan</span>@break
                @default<span class="badge bg-secondary">{{ $p->scan_status }}</span>
            @endswitch
            @if($p->hasPendingUpdate())
                @switch($p->pending_scan_status)
                    @case('pending')<br><span class="badge bg-warning text-dark mt-1"><span class="material-symbols-outlined" style="font-size:.85rem">schedule</span> Update: antri scan</span>@break
                    @case('scanning')<br><span class="badge bg-info mt-1"><span class="material-symbols-outlined" style="font-size:.85rem">security</span> Update: discan</span>@break
                    @case('error')<br><span class="badge bg-danger mt-1"><span class="material-symbols-outlined" style="font-size:.85rem">error</span> Update: scan gagal</span>@break
                @endswitch
            @elseif($p->pending_scan_status === 'infected')
                <br><span class="badge bg-danger mt-1"><span class="material-symbols-outlined" style="font-size:.85rem">gpp_bad</span> Update terakhir: malware</span>
            @endif
        @endif
    </td>
    <td>
        @if($p->is_taken_down)<span class="badge bg-dark">Takedown</span>
        @elseif($p->is_published)<span class="badge bg-success">Published</span>
        @elseif($hasPendingPayment)
            <span class="badge bg-warning text-dark d-block mb-2">Menunggu pembayaran</span>
            <a href="{{ route('developer.platforms.upload-fee',$p->id) }}" class="btn btn-sm btn-warning">Bayar / lanjutkan</a>
        @elseif($isUploadFeePaid && $p->scan_status !== 'clean')
            <span class="badge bg-info text-dark">Menunggu scan</span>
        @else<span class="badge bg-secondary">Menunggu</span>@endif
    </td>
    <td class="text-end">
        <div class="action-group">
        @if($p->is_published && $p->scan_status === 'clean' && !$p->is_taken_down)
            <a href="{{ route('developer.platforms.update-file',$p->id) }}" class="btn btn-sm btn-outline-success" title="Update file installer">
                <span class="material-symbols-outlined" style="font-size:.95rem">cloud_upload</span>
            </a>
        @endif
        <a href="{{ route('developer.platforms.edit',$p->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <form action="{{ route('developer.platforms.destroy',$p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button>
        </form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">Belum ada produk. Klik "Upload Baru" untuk mulai.</td></tr>
@endforelse
</tbody></table>
</div></div>
@endsection
