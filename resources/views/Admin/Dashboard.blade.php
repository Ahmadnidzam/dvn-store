@extends('Layouts.layout')
@section('title', 'Admin Dashboard - DVNStore')
@section('content')
<h2 class="page-title">
    <span class="material-symbols-outlined align-middle fs-1">admin_panel_settings</span>
    Admin Dashboard
</h2>

<div class="row g-3 mb-4">
    @php
        $cards = [
            ['Users',$totalUsers,'group','#0d6efd'],
            ['Developers',$totalDevelopers,'code','#6610f2'],
            ['Games',$totalGames,'sports_esports','#ffc107'],
            ['Aplikasi',$totalApps,'apps','#28a745'],
            ['GMV (Rp)',number_format($totalGMV,0,',','.'),'payments','#fd7e14'],
            ['Pendapatan Platform (Rp)',number_format($totalRevenue,0,',','.'),'account_balance','#20c997'],
            ['Pending Withdraw',$pendingWithdraws,'pending','#dc3545'],
            ['Akun Diblokir',$blockedUsers,'block','#6c757d'],
            ['Platform Takedown',$takenDownPlatforms,'gpp_bad','#e83e8c'],
        ];
    @endphp
    @foreach($cards as [$lbl,$val,$icon,$col])
    <div class="col-lg-4 col-md-6">
        <div class="card stat-card border-0 shadow-sm" style="border-left-color: {{$col}} !important;">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small text-uppercase mb-1">{{ $lbl }}</p>
                    <h3 class="fw-bold mb-0">{{ $val }}</h3>
                </div>
                <span class="material-symbols-outlined" style="font-size: 2.5rem; color: {{$col}}">{{ $icon }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white"><h5 class="mb-0 fw-bold text-brand">10 Transaksi Terakhir</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead><tr><th>Kode</th><th>Tipe</th><th>User</th><th>Platform</th><th class="text-end">Amount</th><th>Paid At</th></tr></thead>
                <tbody>
                    @forelse($recentTransactions as $t)
                    <tr>
                        <td><code>{{ $t->kode_transaksi }}</code></td>
                        <td><span class="badge bg-{{ $t->tipe === 'purchase' ? 'success' : 'info' }}">{{ $t->tipe }}</span></td>
                        <td>{{ $t->user->name ?? '-' }}</td>
                        <td>{{ $t->platform->nama_platform ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($t->amount,0,',','.') }}</td>
                        <td>{{ optional($t->paid_at)->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
