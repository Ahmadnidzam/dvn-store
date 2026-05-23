@extends('Layouts.layout')
@section('title','Withdraw - Developer')
@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
    <h3 class="page-title mb-0"><span class="material-symbols-outlined">payments</span> Withdraw</h3>
    @if($irisEnabled && $wallet->saldo >= $minWithdraw)
        <a href="{{ route('developer.withdraws.create') }}" class="btn btn-success"><span class="material-symbols-outlined">add</span> Request Withdraw</a>
    @else
        <button class="btn btn-secondary" disabled
            title="{{ !$irisEnabled ? 'Fitur withdraw sedang dipersiapkan' : 'Saldo belum mencapai minimum' }}">
            <span class="material-symbols-outlined">add</span> Request Withdraw
        </button>
    @endif
</div>

@unless($irisEnabled)
<div class="alert alert-warning d-flex align-items-center gap-2">
    <span class="material-symbols-outlined">construction</span>
    <div>
        <strong>Fitur withdraw sedang dipersiapkan.</strong><br>
        <small>Integrasi pencairan dana otomatis melalui payment gateway sedang dalam tahap aktivasi. Saldo Anda tetap aman dan akan bisa dicairkan setelah fitur ini diaktifkan.</small>
    </div>
</div>
@endunless

<div class="alert alert-info">
    Saldo: <strong>Rp {{ number_format($wallet->saldo,0,',','.') }}</strong> -
    Minimum withdraw: <strong>Rp {{ number_format($minWithdraw,0,',','.') }}</strong>
</div>

<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table align-middle">
<thead><tr><th>Tanggal</th><th class="text-end">Amount</th><th>Bank</th><th>Status</th><th>Diproses</th></tr></thead>
<tbody>
@forelse($withdraws as $w)
<tr>
    <td>{{ $w->created_at->format('d M Y H:i') }}</td>
    <td class="text-end">Rp {{ number_format($w->amount,0,',','.') }}</td>
    <td><small>{{ strtoupper($w->bank_snapshot['bank_name'] ?? '') }} {{ $w->bank_snapshot['bank_account_number'] ?? '' }}</small></td>
    <td><span class="badge bg-{{ ['pending'=>'warning','processing'=>'info','success'=>'success','failed'=>'danger','rejected'=>'dark'][$w->status] ?? 'secondary' }}">{{ $w->status }}</span></td>
    <td>{{ optional($w->processed_at)->format('d M Y H:i') ?? '-' }}</td>
</tr>
@empty
<tr><td colspan="5" class="text-center text-muted py-4">Belum ada withdraw.</td></tr>
@endforelse
</tbody></table>
{{ $withdraws->links() }}
</div></div>
@endsection
