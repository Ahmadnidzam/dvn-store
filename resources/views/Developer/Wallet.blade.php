@extends('Layouts.layout')
@section('title','Wallet - Developer')
@section('content')
<h3 class="page-title"><span class="material-symbols-outlined">account_balance_wallet</span> Wallet Saya</h3>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card hero-panel shadow-sm text-white">
            <div class="card-body text-center py-4">
                <p class="text-uppercase small mb-1 opacity-75">Saldo Tersedia</p>
                <h1 class="fw-bold mb-3">Rp {{ number_format($wallet->saldo,0,',','.') }}</h1>
                @if(config('dvnstore.iris.enabled'))
                    <a href="{{ route('developer.withdraws.create') }}" class="btn btn-light fw-bold">
                        <span class="material-symbols-outlined align-middle">account_balance</span> Withdraw
                    </a>
                @else
                    <a href="{{ route('developer.withdraws.index') }}" class="btn btn-light fw-bold" title="Fitur withdraw sedang dipersiapkan">
                        <span class="material-symbols-outlined align-middle">account_balance</span> Lihat Riwayat Withdraw
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h6 class="fw-bold mb-0">Riwayat Mutasi</h6></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th class="ps-3">Tanggal</th><th>Tipe</th><th>Deskripsi</th><th class="text-end">Jumlah</th><th class="text-end pe-3">Saldo</th></tr></thead>
                    <tbody>
                        @forelse($mutations as $m)
                        <tr>
                            <td class="ps-3">{{ $m->created_at->format('d M Y H:i') }}</td>
                            <td><span class="badge bg-{{ $m->tipe==='credit'?'success':'danger' }}">{{ $m->tipe }}</span></td>
                            <td><small>{{ $m->description }}</small></td>
                            <td class="text-end {{ $m->tipe==='credit'?'text-success':'text-danger' }}">
                                {{ $m->tipe==='credit'?'+':'-' }} Rp {{ number_format($m->amount,0,',','.') }}
                            </td>
                            <td class="text-end pe-3">Rp {{ number_format($m->saldo_after,0,',','.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada mutasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $mutations->links() }}</div>
        </div>
    </div>
</div>
@endsection
