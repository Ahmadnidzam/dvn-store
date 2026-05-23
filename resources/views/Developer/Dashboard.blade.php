@extends('Layouts.layout')
@section('title', 'Developer Dashboard - DVNStore')
@section('content')
<h2 class="page-title">
    <span class="material-symbols-outlined align-middle fs-1">code</span>
    Developer Dashboard
</h2>

<div class="row g-3 mb-4">
    @php
        $cards = [
            ['Total Produk',$totalPlatforms,'inventory_2','#0d6efd'],
            ['Sudah Publish',$totalPublished,'verified','#28a745'],
            ['Total Download',$totalDownloads,'download','#fd7e14'],
            ['Total Review',$totalReviews,'rate_review','#ffc107'],
            ['Total Pendapatan',$totalRevenue,'payments','#20c997'],
            ['Saldo Wallet',$wallet->saldo,'account_balance_wallet','#6610f2'],
        ];
    @endphp
    @foreach($cards as $i => [$lbl,$val,$icon,$col])
    <div class="col-lg-4 col-md-6">
        <div class="card stat-card border-0 shadow-sm" style="border-left-color: {{$col}} !important;">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small text-uppercase mb-1">{{ $lbl }}</p>
                    <h3 class="fw-bold mb-0">@if($i>=4)Rp {{ number_format($val,0,',','.') }}@else{{ $val }}@endif</h3>
                </div>
                <span class="material-symbols-outlined" style="font-size: 2.5rem; color: {{$col}}">{{ $icon }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h5 class="mb-0 fw-bold">Top 5 Produk (by rating)</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Produk</th><th>Kategori</th><th class="text-center">Download</th><th class="text-center">Review</th><th class="text-end">Rating</th></tr></thead>
                        <tbody>
                            @forelse($topPlatforms as $p)
                            <tr>
                                <td><strong>{{ $p->nama_platform }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $p->category }}</span></td>
                                <td class="text-center">{{ $p->downloads_count }}</td>
                                <td class="text-center">{{ $p->reviews_count }}</td>
                                <td class="text-end"><span class="rating-inline"><span class="material-symbols-outlined">star</span> {{ number_format($p->rating,2) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">Belum ada produk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h5 class="mb-0 fw-bold">Pendapatan per Bulan (12 bulan terakhir)</h5></div>
            <div class="card-body">
                <table class="table table-sm">
                    @forelse($salesPerMonth as $row)
                    <tr><td>{{ $row->bulan }}</td><td class="text-end fw-bold">Rp {{ number_format($row->total,0,',','.') }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted">Belum ada penjualan.</td></tr>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
</div>

<div class="text-end mt-3">
    <a href="{{ route('developer.platforms.create') }}" class="btn btn-success">
        <span class="material-symbols-outlined align-middle">add</span> Upload Produk Baru
    </a>
</div>
@endsection
