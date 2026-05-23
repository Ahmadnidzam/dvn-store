@extends('Layouts.layout')
@section('title', 'Detail Developer - Admin')
@section('content')
<a href="{{ route('admin.developers.index') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="fw-bold">{{ $dev->name }}</h4>
                <p class="text-muted mb-1">{{ $dev->email }}</p>
                <p class="mb-3">Status:
                    @if($dev->status==='blocked')<span class="badge bg-danger">Blocked</span>
                    @else<span class="badge bg-success">Active</span>@endif
                </p>
                @if($dev->developerProfile)
                    <hr>
                    <p class="mb-1"><strong>Studio:</strong> {{ $dev->developerProfile->nama_studio }}</p>
                    @if($dev->developerProfile->website)<p class="mb-1"><strong>Website:</strong> {{ $dev->developerProfile->website }}</p>@endif
                    <p class="mb-1"><strong>Bank:</strong> {{ strtoupper($dev->developerProfile->bank_name) }} - {{ $dev->developerProfile->bank_account_number }}</p>
                    <p class="mb-1"><strong>Atas Nama:</strong> {{ $dev->developerProfile->bank_account_holder }}</p>
                @endif
                <hr>
                <p class="mb-1"><strong>Saldo Wallet:</strong> Rp {{ number_format(optional($dev->wallet)->saldo ?? 0,0,',','.') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Produk Developer ({{ $dev->platforms->count() }})</h6></div>
            <div class="card-body table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Nama</th><th>Kategori</th><th>Status</th><th>Scan</th><th class="text-end">Harga</th></tr></thead>
                    <tbody>
                        @forelse($dev->platforms as $p)
                        <tr>
                            <td><a href="{{ route('admin.platforms.show',$p->id) }}">{{ $p->nama_platform }}</a></td>
                            <td>{{ $p->category }}</td>
                            <td>
                                @if($p->is_taken_down)<span class="badge bg-dark">Taken Down</span>
                                @elseif($p->is_published)<span class="badge bg-success">Published</span>
                                @else<span class="badge bg-secondary">Draft</span>@endif
                            </td>
                            <td>{{ $p->scan_status }}</td>
                            <td class="text-end">Rp {{ number_format($p->harga,0,',','.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">Belum ada produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Riwayat Withdraw</h6></div>
            <div class="card-body table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Tanggal</th><th>Amount</th><th>Status</th><th>Ref No</th></tr></thead>
                    <tbody>
                        @forelse($dev->withdraws as $w)
                        <tr>
                            <td>{{ $w->created_at->format('d M Y H:i') }}</td>
                            <td>Rp {{ number_format($w->amount,0,',','.') }}</td>
                            <td><span class="badge bg-info">{{ $w->status }}</span></td>
                            <td><code>{{ $w->iris_payout_reference_no ?: '-' }}</code></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada withdraw.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
